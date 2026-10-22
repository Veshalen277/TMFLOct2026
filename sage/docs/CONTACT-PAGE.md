# Contact page — Contact Form 7

## Set it up once

1. Install this updated theme in the existing `sage` folder and keep Contact Form 7 active.
2. In WordPress, create or edit your Contact Us page. Under the page's template setting, choose **Contact — Monochrome**.
3. Add your existing Contact Form 7 block or paste the exact shortcode from **Contact → Contact Forms** into a Shortcode block in the page content. Keep its actual ID, whether numeric or alphanumeric. Do not add an HTML `<form>` wrapper.
4. Publish/update the page. Add it to your navigation under Appearance → Menus if needed.
5. Optional: in **Appearance → Customize → Contact Page**, enter your public email, phone, address, hours and introduction. Blank details stay hidden. The WordPress administrator email is never automatically displayed.

Your existing CF7 form needs no custom field classes. Its standard text fields, email fields, textarea, selects, choices, attachments, submit button and response messages inherit the theme. Forms elsewhere in the theme also inherit the same styling.

## Optional starter form

`cf7-starter-form.txt` contains a ready-to-paste CF7 form layout: name/email side by side on desktop, then subject/message. Paste it into the CF7 **Form** tab only if you want to replace that form's layout. It preserves the default `your-name`, `your-email`, `your-subject` and `your-message` mail-tag names. The single optional `sage-form-row` wrapper supplies the two-column layout; individual field classes are not needed. Ordinary CF7 form layouts work too.

Keep your real recipients, From address, Reply-To and message body in CF7's **Mail** settings. The theme does not configure email delivery, change recipients, send test messages or add a second form processor. After installation, send your own test submission and verify receipt. If mail delivery is already configured, these settings can stay as they are.

## Why this is less fragile

The CSS is in `resources/css/contact.css` and is included in the compiled theme assets. It scopes to CF7's documented `.wpcf7` wrapper, ordinary HTML input types, `.wpcf7-not-valid-tip`, `.wpcf7-response-output`, and form status classes. It does not depend on generated IDs like `wpcf7-f123-p456-o1`, particular field names or a fixed field order. You can rename fields, duplicate a form or replace the form ID without rewriting theme CSS.

These hooks are documented by CF7; no third-party plugin markup can be guaranteed never to change. After major plugin updates, check one successful submission and one validation error. No field-by-field restyling should be necessary for routine use.

Status messages remain textual and visible in monochrome, with a distinct dark success panel and outlined validation fields. CF7's own error handling, loading spinner, screen-reader output, anti-spam and submission logic remain intact. No theme JavaScript intercepts submission.

## Appearance and safe defaults

The contact page uses the existing Cormorant Garamond/Inter pairing, spacious full-width heading, a concise contact-information column and a bordered message panel. Phone/email links are clickable. A privacy-policy link appears only when WordPress has a policy URL configured. No business hours, response-time promises, contact details or consent claims are invented.

An empty form area shows a setup hint only to editors; visitors receive an unavailable message. Deactivated CF7 shortcodes are not printed as raw shortcode text on this template. You must still add the real form in WordPress—this ZIP does not contain or modify your site's database.

## Validation

The production build passes and a representative CF7 markup fixture was generated. The local Chromium preview process would not start in this session, so visual/browser checks could not be completed for this page. Check the desktop/mobile layout, one invalid submission and one successful submission on your WordPress staging site. Actual plugin submissions, spam protection and mail delivery have not been tested here.

Official references:
- https://contactform7.com/styling-contact-form/
- https://contactform7.com/2023/12/17/form-status/
- https://contactform7.com/faq/can-i-add-id-and-class-attributes-to-a-form-element/
