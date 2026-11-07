# 12. Roadmap and build order

## Milestone 1: Foundation (2-3 days)
- Laravel install, auth, custom admin UI, `config/school.php`
- Migrations and seeders (courses, instructors)
- Student, Course, Instructor resources

## Milestone 2: Certificate builder (3-4 days)
- Certificate resource with monthly attendance repeater
- Calculations, "Suggest monthly hours", validation rules
- Japanese date helper

## Milestone 3: PDF (3-4 days)
- Extract assets from the Word file, install fonts
- Blade template matching the current design
- PDF service, preview with DRAFT watermark
- Tune until it matches the Word version on A4

## Milestone 4: QR and verification (2 days)
- Number and code generators
- QR service, QR on PDF
- Public verify pages, logging, rate limiting
- Revoke action

## Milestone 5: Hardening and launch (2-3 days)
- Policies, immutability observer, 2FA
- Tests from doc 11
- Server setup, HTTPS, backups, monitoring
- Staff training with doc 8

## Milestone 6: Nice to have
| Feature | Value |
|---|---|
| CSV bulk import | Fast issuing for whole batches |
| Email certificate to student | Saves manual work |
| Student portal / download link | Self-service re-download |
| Multiple courses (N4, N3, Speaking, etc.) | Reuse the same template with different course data |
| Multiple templates / languages | Other certificate designs |
| Digital signature on the PDF | Extra trust |
| Public stats dashboard | Number of graduates per year |
| Partner API keys | Verification for employers and Japanese schools |
| Audit trail UI | Who issued and revoked what |

## Open questions to decide before coding
1. Final domain for the verify URL (permanent, printed in QR).
2. Which email address is official: `.uk` or `.co.uk`?
3. Should signatures be printed images or left blank for hand signing?
4. Is the monthly attendance always 5 months, or variable? (The system supports any number; the table width in the design will need to adapt.)
5. Who may revoke certificates?
6. Should the student's date of birth stay on the certificate? (It is not shown on the public verify page either way.)
7. Which other courses (N4, N3, etc.) need a certificate and do they share the same design?
