@extends('layouts.admin')

@section('title', 'System Settings')
@section('eyebrow', 'System Configuration')

@section('topbar_actions')
    <a class="btn btn-ghost" href="{{ route('admin.dashboard') }}">&larr; Back to dashboard</a>
@endsection

@section('content')
    <form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('put')

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>School Information</h2>
                    <p>These institutional details appear on certificates, verification pages, and correspondence.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field span-2">
                        <label for="school_name">Official School Name</label>
                        <input id="school_name" class="input" name="school_name" type="text" required
                               value="{{ $settings['school_name'] ?? '' }}">
                        @error('school_name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field span-2">
                        <label for="school_address">Campus Address</label>
                        <textarea id="school_address" class="textarea" name="school_address" required rows="3">{{ $settings['school_address'] ?? '' }}</textarea>
                        @error('school_address')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="school_phone">Contact Phone</label>
                        <input id="school_phone" class="input" name="school_phone" type="text" required
                               value="{{ $settings['school_phone'] ?? '' }}">
                        @error('school_phone')<div class="error-text">{{ $message }}</div>@enderror
                    </div>

                    <div class="field">
                        <label for="school_email">Official Email</label>
                        <input id="school_email" class="input" name="school_email" type="email" required
                               value="{{ $settings['school_email'] ?? '' }}">
                        @error('school_email')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Verification & Numbering Configuration</h2>
                    <p>Root URL encoded into QR codes and certificate numbering prefixes.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field span-2">
                        <label for="verify_base_url">Verification Base URL</label>
                        <input id="verify_base_url" class="input" name="verify_base_url" type="url" required
                               value="{{ $settings['verify_base_url'] ?? '' }}">
                        @error('verify_base_url')<div class="error-text">{{ $message }}</div>@enderror
                        <div class="help">Example: https://certificates.eminentoverseas.com/verify</div>
                    </div>

                    <div class="field">
                        <label for="number_prefix">Certificate Number Prefix</label>
                        <input id="number_prefix" class="input mono" name="number_prefix" type="text" required
                               value="{{ $settings['number_prefix'] ?? 'EOC' }}">
                        @error('number_prefix')<div class="error-text">{{ $message }}</div>@enderror
                        <div class="help">Prepended to certificate numbers (e.g., EOC-2026-000001)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <div>
                    <h2>Certificate Graphical Assets</h2>
                    <p>High-resolution images embedded directly in DomPDF certificate generation.</p>
                </div>
            </div>

            <div class="card-body">
                <div class="form-grid">
                    <div class="field">
                        <label for="logo_path">School Logo</label>
                        @if ($settings['logo_path'] ?? false)
                            <div class="current-asset">
                                <img src="{{ asset($settings['logo_path']) }}" alt="Current logo" style="max-height: 56px;">
                                <span>Custom logo active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_logo" value="1">
                                <span>Reset to default logo</span>
                            </label>
                        @endif
                        <input id="logo_path" class="input" name="logo_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: 300x300px transparent PNG</div>
                    </div>

                    <div class="field">
                        <label for="background_path">A4 Certificate Border Background</label>
                        @if ($settings['background_path'] ?? false)
                            <div class="current-asset">
                                <img src="{{ asset($settings['background_path']) }}" alt="Current background" style="max-height: 56px;">
                                <span>Custom border template active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_background" value="1">
                                <span>Reset to default border background</span>
                            </label>
                        @endif
                        <input id="background_path" class="input" name="background_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: A4 2480x3508px portrait</div>
                    </div>

                    <div class="field">
                        <label for="divider_path">Gold Header Divider</label>
                        @if ($settings['divider_path'] ?? false)
                            <div class="current-asset">
                                <img src="{{ asset($settings['divider_path']) }}" alt="Current divider" style="max-height: 56px;">
                                <span>Custom divider active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_divider" value="1">
                                <span>Reset to default divider</span>
                            </label>
                        @endif
                        <input id="divider_path" class="input" name="divider_path" type="file" accept="image/*">
                        <div class="help">PNG, max 2MB. Recommended: wide tapered gold line (e.g. 1400x20px).</div>
                    </div>

                    <div class="field">
                        <label for="seal_path">Official School Seal / Stamp</label>
                        @if ($settings['seal_path'] ?? false)
                            <div class="current-asset">
                                <img src="{{ asset($settings['seal_path']) }}" alt="Current seal" style="max-height: 56px;">
                                <span>Custom seal active</span>
                            </div>
                            <label class="check" style="margin-bottom: 6px;">
                                <input type="checkbox" name="reset_seal" value="1">
                                <span>Reset to default seal</span>
                            </label>
                        @endif
                        <input id="seal_path" class="input" name="seal_path" type="file" accept="image/*">
                        <div class="help">PNG or JPG, max 2MB. Recommended: 300x300px transparent PNG</div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a class="btn btn-secondary" href="{{ route('admin.dashboard') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Save Settings</span>
                </button>
            </div>
        </div>
    </form>
@endsection
