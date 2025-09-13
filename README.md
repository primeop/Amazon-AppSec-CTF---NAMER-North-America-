# Amazon AppSec CTF - NAMER North America Writeups

Welcome to my collection of writeups from the Amazon AppSec CTF (North America region). This repository contains detailed technical analysis and solutions for various cybersecurity challenges across different categories.

## 🏆 CTF Overview

**Event**: Amazon AppSec CTF - NAMER North America  
**Duration**: [Event Duration]  
**Categories**: Web Security, Cloud Security, AI/ML Security, Secure Coding  
**Total Challenges**: 7+ challenges completed  

## 📁 Challenge Categories

### 🌐 Web Security Challenges

#### 1. Evil Core Employee Portal
- **Type**: Multi-vulnerability Web Application
- **Vulnerabilities**: SQL Injection, Arbitrary File Write, PHP Code Injection, Access Control Bypass
- **Impact**: Remote Code Execution (RCE)
- **Difficulty**: High
- **Writeup**: [`web/web_evil_reviews/readme.md.md`](web/web_evil_reviews/readme.md.md)

**Key Learning**: Chaining multiple vulnerabilities (SQL injection → file write → code injection) to achieve complete system compromise.

#### 2. Council of Ricks
- **Type**: XML External Entity (XXE) Attack
- **Vulnerability**: XML processing with `LIBXML_NOENT` flag
- **Impact**: Local file disclosure, SSRF potential
- **Difficulty**: Medium
- **Writeup**: [`secure coding/council_of_ricks/WRITEUP.md`](secure coding/council_of_ricks/WRITEUP.md)

**Key Learning**: Proper XML processing security and the dangers of external entity loading.

### ☁️ Cloud Security Challenges

#### 3. Leak (CTF Cloud)
- **Type**: Cloud Infrastructure Enumeration
- **Vulnerability**: Exposed `.git` repository leading to LocalStack configuration leak
- **Impact**: AWS credentials exposure, Lambda function access
- **Difficulty**: Medium
- **Writeup**: [`cloud/readme.md.md`](cloud/readme.md.md)

**Key Learning**: Git repository exposure in cloud environments and LocalStack security implications.

### 🤖 AI/ML Security Challenges

#### 4. Re-Cars AI
- **Type**: Prompt Injection Attack
- **Vulnerability**: AI model output transformation bypass
- **Impact**: Sensitive data exfiltration (phone number)
- **Difficulty**: Medium
- **Writeup**: [`AI/Re-Cars AI/readme.md`](AI/Re-Cars%20AI/readme.md)

**Key Learning**: AI security requires data-level access controls, not just prompt-level restrictions.

#### 5. AllSafe Tickets
- **Type**: AI Classification Manipulation
- **Vulnerability**: Prompt injection and keyword priming
- **Impact**: Ticket priority escalation
- **Difficulty**: Low-Medium
- **Writeup**: [`AI/AllSafe Tickets/readme.md.md`](AI/AllSafe%20Tickets/readme.md.md)

**Key Learning**: AI systems need robust input validation and separation of concerns.

### 🔒 Secure Coding Challenges

#### 6. Space Uber - SSRF Protection
- **Type**: Server-Side Request Forgery (SSRF)
- **Vulnerability**: Action persistence in API endpoints
- **Impact**: Unauthorized data persistence
- **Difficulty**: Medium
- **Writeup**: [`secure coding/space_uber/CTF_Writeup_SSRF_Protection.md`](secure coding/space_uber/CTF_Writeup_SSRF_Protection.md)

**Key Learning**: SSRF protection requires application-layer controls and proper action validation.

#### 7. Lost Doctorhood
- **Type**: SQL Injection + File Upload Bypass
- **Vulnerabilities**: ORDER BY injection, file upload bypass, path traversal
- **Impact**: Database compromise, arbitrary file upload
- **Difficulty**: High
- **Writeup**: [`secure coding/lost_doctorhood/SECURITY_WRITEUP.md`](secure coding/lost_doctorhood/SECURITY_WRITEUP.md)

**Key Learning**: Whitelist validation is more secure than blacklist filtering for both SQL and file uploads.

## 🛠️ Technical Skills Demonstrated

### Web Application Security
- SQL Injection exploitation and prevention
- File upload security and bypass techniques
- XML External Entity (XXE) attacks
- Server-Side Request Forgery (SSRF)
- Access control bypass techniques
- Code injection and RCE

### Cloud Security
- AWS/LocalStack enumeration
- Git repository security
- Cloud credential management
- Lambda function security

### AI/ML Security
- Prompt injection attacks
- AI model manipulation
- Data exfiltration through AI systems
- Classification system bypass

### Secure Coding
- Input validation and sanitization
- Output encoding and escaping
- Defense in depth principles
- Security testing methodologies

## 📊 Challenge Statistics

| Category | Challenges | Completed | Success Rate |
|----------|------------|-----------|--------------|
| Web Security | 2 | 2 | 100% |
| Cloud Security | 1 | 1 | 100% |
| AI/ML Security | 2 | 2 | 100% |
| Secure Coding | 2 | 2 | 100% |
| **Total** | **7** | **7** | **100%** |

## 🔧 Tools and Techniques Used

### Reconnaissance
- `nmap` - Network scanning
- `whatweb` - Web technology fingerprinting
- `GitTools` - Git repository extraction
- Manual code analysis

### Exploitation
- `curl` - HTTP request manipulation
- `requests` (Python) - Automated exploitation
- Custom Python scripts
- Base64 encoding/decoding
- XML payload crafting

### Cloud Security
- AWS CLI with LocalStack
- Git repository analysis
- Lambda function manipulation
- Cloud service enumeration

### AI Security
- Prompt engineering
- Output transformation techniques
- Classification manipulation
- Social engineering tactics

## 🚀 Upcoming Updates

I'm currently working on several improvements to this repository:

### 📝 Content Updates
- [ ] **Detailed exploit scripts** - Adding more comprehensive PoC code
- [ ] **Video walkthroughs** - Screen recordings of exploitation process
- [ ] **Defense strategies** - Detailed mitigation recommendations
- [ ] **Additional challenges** - More writeups from other CTF events

### 🔧 Technical Improvements
- [ ] **Interactive demos** - Docker containers for hands-on practice
- [ ] **Automated testing** - Scripts to verify fixes
- [ ] **Security checklists** - Best practices for each vulnerability type
- [ ] **Performance analysis** - Impact assessment of security fixes

### 📚 Documentation
- [ ] **Learning paths** - Structured curriculum for each security domain
- [ ] **Reference guides** - Quick lookup for common vulnerabilities
- [ ] **Tool comparisons** - Analysis of different security tools
- [ ] **Industry insights** - Real-world application of CTF techniques

## 🤝 Contributing

This repository is open for educational purposes. If you have:
- Additional writeups from the same CTF
- Improvements to existing solutions
- Better exploitation techniques
- Additional mitigation strategies

Please feel free to submit a pull request or open an issue.

## ⚠️ Disclaimer

All writeups and techniques described in this repository are for **educational purposes only**. These methods should only be used on:
- Systems you own
- Authorized penetration testing engagements
- CTF competitions
- Educational environments

**Never use these techniques on systems without proper authorization.**

## 📞 Contact

- **GitHub**: [@primeop](https://github.com/primeop)
- **CTF Team**: [Your Team Name]
- **Writeup Questions**: Open an issue in this repository

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🏅 Achievements

- ✅ **100% Challenge Completion Rate**
- ✅ **Multi-domain Security Expertise** (Web, Cloud, AI, Coding)
- ✅ **Advanced Exploitation Techniques**
- ✅ **Comprehensive Documentation**
- ✅ **Real-world Security Insights**

*Last Updated: January 2025*

---

**Happy Hacking! 🔐**
