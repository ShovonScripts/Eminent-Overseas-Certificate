# 8. Admin guide (for staff)

## 8.1 Login
Open `/login` (or `/admin`, which redirects there) and sign in with the account created by the administrator.

## 8.2 One-time setup
1. **Instructors**: add each instructor (name, title, email, signature image optional).
2. **Courses**: confirm "日本語コース / Basic Level" (180 hours, JLPT N5, Minna No Nihongo).

## 8.3 Issue a certificate

1. **Students → New**: enter name, date of birth, gender, contact details.
2. **Certificates → New**:
   1. Choose the student and the course. Course details fill in automatically.
   2. Enter study start and end dates.
   3. Tick the class days (for example Saturday, Monday, Wednesday) and hours per day.
   4. Enter national holidays separated by `;`, for example `6月9日; 6月11日`, or as `2025-06-09`.
   5. Click **Suggest monthly hours**. Edit each month if needed.
   6. Enter attendance hours for each month. The rate is calculated.
   7. Pick the two instructors (left and right).
   8. Set the issue date (発行日).
3. Click **Preview** to stream a draft PDF with a DRAFT watermark. Check every field, especially spelling of the name and dates.
4. Click **Issue**. The system assigns the certificate number, QR code, and saves the PDF.
5. Click **Download PDF** and print on A4. Print at **100% scale** (not "fit to page").

## 8.4 Correcting a mistake
- **Draft**: just edit it.
- **Already issued**: only a super admin can click **Revoke**; enter the reason, then create a corrected certificate from scratch. The old QR will show "Revoked".

## 8.5 Bulk issue (planned)
A CSV import is on the roadmap, not yet available in the admin panel. Columns planned:

```
student_code,course,study_start,study_end,class_days,hours_per_day,issued_on,
june,july,august,september,october
```

## 8.6 Checking a certificate
Scan the QR or open `/verify` and type the certificate number. The **Verification Logs** page in the admin panel shows who checked what and when.

## 8.7 Printing tips
- Use 100 gsm or heavier paper; 120-160 gsm is nicer.
- Print in colour.
- Do not enable "shrink to fit".
- Test the QR code after printing.
- Sign by hand and add the school stamp if your process requires it.

## 8.8 Who to contact
Technical issues: the developer or system administrator.
