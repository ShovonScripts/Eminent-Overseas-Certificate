# 1. Requirements

## 1.1 Purpose
Replace manual editing of a Word file with a system that creates consistent, verifiable certificates in seconds.

## 1.2 Users

| Role | Can do |
|---|---|
| Super admin | Everything, manage users, settings |
| Staff | Manage students, create and issue certificates, download PDFs |
| Public (anyone) | Open the verify page by scanning the QR code or typing the code |

## 1.3 Fields found on the current certificate

### Fixed (school) information
| Field | Current value |
|---|---|
| School name | Eminent Overseas & Consultants |
| Address | 16/9, Indira Road, Behind Tejgaon College, Dhaka 1215 |
| Phone | +88 01816877795 |
| Contact email | info@eminentoverseas.uk (shown on certificate) |
| Logo, border, seal | Image assets |

> The Word file displays `info@eminentoverseas.uk` but the link target is `info@eminentoverseas.co.uk`. Decide on one address and keep it in `config/school.php`.

### Per-instructor
| Field | Example |
|---|---|
| Name | Sheikh Monujurl Islam, Abdul Kaium Ovi |
| Title | 担当講師 / Instructor |
| Email | shown under name |
| Signature | image (optional) |

### Per-student
| Field (JA / EN) | Example |
|---|---|
| 学生氏名 / Name | Md. Anik |
| 生年月日 / Date of Birth | 2004年12月9日 |
| 性別 / Sex | 男性 (Male) |

### Per-course / certificate
| Field | Example |
|---|---|
| 学習期間 / Study Period | 2025年6月2日 ～ 2025年10月27日 |
| 学習時間 / Learning Time | 1日3時間 週3日 / 3 hours a day, 3 days per week |
| Class days | 土曜日, 月曜日 そして 水曜日 / Saturday, Monday and Wednesday |
| Monthly table | June to October: Class Hour, Attendance Hour, Attendance Rate (%), Sum |
| 他の休み / Other Holidays | National holidays: 6月9日; 6月11日; 8月16日; 10月1日 |
| 履修内容 / Learning Context | 会話、語彙、文法、読解、聴解、作文 / Conversation, Vocabulary, Grammar, Reading, Listening, Writing |
| コース名称 / Course Name | 日本語初級 / Basic Level |
| 総履修時間 / Course Completion Time | 180時間 / 180 Hours |
| 当該コース終了目標レベル / Learning Aim Level | JLPT N5 修了相当 / N5 Level |
| 使用教材 / Book Name | みんなの日本語 / Minna No Nihongo |
| 発行日 / Date of Issued | 2026年1月15日 |

## 1.4 New fields added by the system
| Field | Why |
|---|---|
| Certificate number | Human-readable reference, e.g. `EOC-2026-000001` |
| Verification code | Random ULID used in the QR URL |
| QR code | Printed on certificate, links to verify page |
| Status | draft / issued / revoked |
| PDF hash (SHA-256) | Detect tampering with stored PDFs |

## 1.5 Business rules
1. Study end date must be after the start date.
2. Attendance hours per month cannot exceed class hours.
3. `Total class hours` = sum of monthly class hours. `Total attendance hours` = sum of monthly attendance hours.
4. `Attendance rate (%)` = attendance / class × 100, rounded to a whole number. The total column uses the overall figures.
5. Issue date cannot be earlier than the study end date.
6. A certificate can only be issued if student, course, at least one instructor, and the monthly table exist.
7. Once **issued**, data cannot be edited. Use **revoke + re-issue**.
8. Certificate numbers are unique and never reused, even after revocation.
9. Verification page shows only: name, course, study period, total hours, issue date, status, certificate number.
10. Date of birth, phone, and email are never shown publicly.

## 1.6 Non-functional requirements
- PDF renders Japanese and English correctly on any machine (embedded font).
- Verify page loads in under 1 second and works on mobile.
- Verify endpoint is rate limited.
- Admin UI available in English (Japanese labels are only on the certificate).
- Daily database backup.

## 1.7 Out of scope for version 1
Online payments, student login portal, automatic email delivery (see roadmap).
