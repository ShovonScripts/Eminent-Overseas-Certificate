# 9. Security checklist

## 9.1 Threats and mitigations

| Threat | Mitigation |
|---|---|
| Someone fakes a certificate | QR points to your domain; fake certificates cannot have a valid code |
| Someone copies a real QR onto another document | Verify page shows the student's name and dates; verifier compares with the document in hand |
| Guessing codes | 26-char random ULID, rate limit, 404 on unknown |
| Enumerating by certificate number | Number lookup is rate limited and returns minimal data |
| Leaking personal data | Verify page hides DOB, phone, email, address |
| PDF tampering | SHA-256 stored; optional PDF upload check |
| Staff edits after issue | Model observer blocks edits; revoke + re-issue |
| Unauthorized PDF download | PDFs stored on private disk, served by authorized controller |
| Admin account takeover | Strong passwords, login throttling, optional 2FA |
| SQL injection / XSS | Eloquent bindings, Blade escaping (`{{ }}`), never `{!! !!}` with user data |
| Scraping | `X-Robots-Tag: noindex`, rate limiting |

## 9.2 Must-do list

- [ ] HTTPS only, HSTS enabled
- [ ] `APP_DEBUG=false` in production
- [ ] `.env` outside web root, not in Git
- [ ] Separate DB user with limited rights
- [ ] Two-factor authentication for all staff
- [ ] Policies on every admin controller
- [ ] Immutability observer on issued certificates
- [ ] Throttle on `/verify` routes
- [ ] Daily encrypted backup, restore tested
- [ ] Log verification attempts
- [ ] `composer audit` in CI

## 9.3 Immutability observer

```php
// app/Observers/CertificateObserver.php
public function updating(Certificate $c): void
{
    if ($c->getOriginal('status') === 'issued') {
        $allowed = ['status','revoked_at','revoke_reason','updated_at'];
        $illegal = array_diff(array_keys($c->getDirty()), $allowed);
        abort_if($illegal, 422, 'Issued certificates cannot be edited. Revoke and re-issue.');
    }
}
```

## 9.4 Security headers (middleware)

```
Strict-Transport-Security: max-age=31536000; includeSubDomains
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Referrer-Policy: no-referrer
Content-Security-Policy: default-src 'self'; img-src 'self' data:
```

## 9.5 Privacy
Student data (especially date of birth) is personal data. Keep access limited to staff, keep backups encrypted, and delete or anonymize records when your retention policy requires it.
