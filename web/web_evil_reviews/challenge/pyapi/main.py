import io
from flask import Flask, abort, jsonify, request, send_file
from wordcloud import WordCloud
import matplotlib.pyplot as plt
import io
import pymysql
import os
from datetime import datetime

app = Flask(__name__)

# Database configuration
db_config = {
    'host': '127.0.0.1',
    'user': 'ecore_user',
    'password': 'ecore_pass',
    'db': 'ecore_db',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}

def get_db_connection():
    return pymysql.connect(**db_config)

@app.before_request
def handle_chunking():
    request.environ["wsgi.input_terminated"] = True



# /api/processor/word-cloud.png
# Generates a word cloud image from a performance review's content
# Required query parameter: review (the review ID)
# Returns: PNG image of the word cloud
# Errors: 400 if review ID missing, 404 if review not found, 500 for server errors
# Access: User

@app.route("/api/processor/word-cloud.png")
def review_wordcloud():
    review_id = request.args.get('review')
    
    if not review_id:
        abort(400, "Review ID is required")
    
    try:
        # Get review content from database
        conn = get_db_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT content FROM performance_reviews WHERE id = %s", (review_id,))
        review = cursor.fetchone()
        conn.close()
        
        if not review:
            abort(404, "Review not found")
        
        text = review['content']
        
        # Generate wordcloud
        wordcloud = WordCloud(width=800, height=800, background_color='white').generate(text)
        plt.figure(figsize=(8,8))
        plt.imshow(wordcloud)
        plt.axis('off')

        # Save plot to bytes buffer
        buf = io.BytesIO()
        plt.savefig(buf, format='png')
        buf.seek(0)
        plt.close()

        return send_file(buf, mimetype='image/png')
    
    except Exception as e:
        abort(500, str(e))



# /api/processor/admin/export/reviews
# Exports performance reviews data to a file
# Optional query parameter: filename
# Returns: File containing review data including employee name, manager name,
#          rating, content and review date
# Access: Admin

@app.route("/api/processor/admin/export/reviews", methods=['GET', 'POST'])
def export_reviews():
    filename = request.args.get('filename')

    try:
        conn = get_db_connection()
        cursor = conn.cursor()

        cursor.execute("""
            SELECT r.id, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                   CONCAT(m.first_name, ' ', m.last_name) as manager_name,
                   r.rating, r.content, r.review_date
            FROM performance_reviews r
            JOIN users e ON r.user_id = e.id
            JOIN users m ON r.reviewer_id = m.id
        """)

        reviews = cursor.fetchall()
        conn.close()

        # Create CSV in memory
        output = io.StringIO()
        headers = ['ID', 'Employee', 'Reviewer', 'Rating', 'Content', 'Date']

        output.write(','.join(headers) + '\n')
        for review in reviews:
            content = review['content'].replace('"', '""')  # Escape double quotes for CSV
            row = [
                str(review['id']),
                review['employee_name'],
                review['manager_name'],
                str(review['rating']) if review['rating'] else '',
                f'"{content}"',
                review['review_date'].strftime('%Y-%m-%d')
            ]
            output.write(','.join(row) + '\n')

        # Use provided filename or generate a generic one
        if not filename:
            filename = f"performance_reviews_{datetime.now().strftime('%Y%m%d_%H%M%S')}.xlsx"

        # Save to file
        file_path = os.path.join('/pyapi/exports/', filename)

        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(output.getvalue())

        # Send success response
        return jsonify({
            'success': True,
            'message': f'Reviews exported to {file_path}'
        })

    except Exception as e:
        abort(500, str(e))

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=3000)
