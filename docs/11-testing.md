# 11. Testing

## 11.1 Test plan

| Area | Test |
|---|---|
| Calculations | totals and rate correct, zero class hours does not divide by zero |
| Issuing | draft → issued sets number, code, PDF path, hash |
| Number generator | sequential per year, unique, never reused after delete |
| Immutability | editing an issued certificate fails |
| Verify | valid → 200, revoked → 200 with banner, unknown → 404, draft → 404 |
| Privacy | verify page never contains DOB, phone, email |
| Logging | each verify call writes `verification_logs` |
| Rate limit | 31st request in a minute returns 429 |
| PDF | generation succeeds, file is a valid PDF, size > 0 |
| Permissions | staff cannot revoke or delete if policy says so |
| Japanese date | `2025-06-02` → `2025年6月2日` |

## 11.2 Sample tests (Pest or PHPUnit)

```php
it('shows a valid certificate without personal data', function () {
    $cert = Certificate::factory()->issued()->create();

    $this->get("/verify/{$cert->verification_code}")
        ->assertOk()
        ->assertSee($cert->certificate_no)
        ->assertSee($cert->student->name_en)
        ->assertDontSee($cert->student->date_of_birth->format('Y-m-d'))
        ->assertDontSee($cert->student->phone);

    $this->assertDatabaseHas('verification_logs', ['certificate_id' => $cert->id, 'result' => 'valid']);
});

it('shows revoked status', function () {
    $cert = Certificate::factory()->revoked()->create();
    $this->get("/verify/{$cert->verification_code}")->assertOk()->assertSee('revoked', false);
});

it('returns 404 for draft and unknown codes', function () {
    $draft = Certificate::factory()->create();   // draft
    $this->get("/verify/{$draft->verification_code}")->assertNotFound();
    $this->get('/verify/01ARZ3NDEKTSV4RRFFQ69G5FAV')->assertNotFound();
});

it('formats Japanese dates', function () {
    expect(JapaneseDate::full(Carbon::parse('2025-06-02')))->toBe('2025年6月2日');
});

it('blocks editing issued certificates', function () {
    $cert = Certificate::factory()->issued()->create();
    expect(fn () => $cert->update(['study_end' => now()]))->toThrow(HttpException::class);
});

it('generates a PDF', function () {
    Storage::fake('local');
    $cert = Certificate::factory()->issued()->withAttendance()->create();
    app(CertificatePdfService::class)->generate($cert);
    Storage::disk('local')->assertExists($cert->fresh()->pdf_path);
});
```

## 11.3 Manual QA checklist before go-live
- [ ] Issue a certificate for yourself and compare it side by side with the original Word certificate
- [ ] All Japanese characters render (including `～` and `★`)
- [ ] Certificate fits on exactly one A4 page
- [ ] QR scans on iPhone and Android, from print and from screen
- [ ] Revoke it, scan again, confirm "Revoked"
- [ ] Open verify page on a phone, check layout
- [ ] Confirm no DOB or phone on the verify page
- [ ] Restore a backup on a test server
