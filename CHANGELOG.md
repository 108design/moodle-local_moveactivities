# Changelog

## 1.6.0 (2026103000) - 2026-10-30

Licence update for compliance with the Moodle Marketplace Provider Terms of Use. Adds Section 6a, a limited permission for Moodle to host, distribute, translate, adapt, promote and review the submitted Software and listing materials, including use by authorised contractors. The 108design source-available licence remains in place; customer rights and third-party licences are preserved. Runtime behaviour is unchanged from GitHub release 1.5.0.

## 1.3.0 - Unreleased

- Search courses through Moodle autocomplete without loading every course into the page.
- Render selection, move confirmation and job history with Mustache templates and AMD modules.
- Use theme colours for the action area and support narrow layouts.
- Require native permissions before creating target sections or deleting source activities.
- Validate the job owner, source activities and current course permissions before queuing; require POST and a valid session key for creation and retry.
- List and delete job owners through the Moodle Privacy API; ignore queued tasks whose job was erased.
- Fix help-module loading on Moodle 5.2 and add field help, linked topics and German translations.

## 1.2.0 - 2026-10-17

- Standardise the contextual-help header and include verified AMD source maps.

## 1.1.0 - 2026-10-16

- Add product logos to administration and contextual help.

## 1.0.3 - 2026-10-08

- Extend declared compatibility to Moodle 5.3 after native plugin checks.


## 1.0.2 — 2026-10-05

- Add the product logo above the README title at 125 by 125 pixels.
- Clarify current-release licence and availability.
- Plugin functionality and complete licence terms are unchanged.
