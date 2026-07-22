# Changelog

All notable changes to **assignsubmission_byblos** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] — 2026-06-10

Aligns the submission picker with the Byblos publish/share model, and promotes the
plugin to beta. A portfolio is now handed in for assessment by *publishing* it, not
by sharing it.

### Added
- The submission picker lists only **published** pages, and collections that hold
  at least one published page. An in-form notice explains this, and the empty-state
  message points students to publish their work first. This stops a student
  accidentally submitting an unfinished draft, and gives publishing a clear purpose:
  it is what makes a portfolio available to submit.

### Changed
- Maturity raised from `MATURITY_ALPHA` to `MATURITY_BETA`.
- Plugin version bumped to `2026072200` and release to `0.2.0`.

### Fixed
- `privacy\provider::get_metadata()` called `collection::link_plugin()`, which is
  not part of the `core_privacy` API and has never existed. Any visit to
  *Site administration → Users → Privacy and policies → Plugin privacy registry*
  raised `Call to undefined method core_privacy\local\metadata\collection::link_plugin()`
  and aborted the registry page. The plugin now declares the
  `local_byblos_submission` table it actually reads and deletes from, via
  `add_database_table()`, with a description for every field.
- `export_submission_user_data()` fell back to querying `local_byblos_submission`
  on a `submissionid` column that does not exist in the table, so any real subject
  access request for a student without a matching `assignsubmissionid` row would
  have thrown a DML exception. The dead fallback has been removed.
- Restored a green CI build. `MOODLE_INTERNAL` checks were stripped from the six
  namespaced `classes/event/` classes (moodle-cs flags them as unnecessary, and
  the workflow runs `phpcs --max-warnings 0`), and an over-length line in
  `submission_updated::get_description()` was wrapped.
- `test_get_form_elements_renders_without_portfolios` and
  `test_is_empty_detects_no_selection` errored in CI with *Class
  `local_byblos\page` not found*, because the workflow installs this subplugin
  without its `local_byblos` dependency. Both now skip (or narrow their
  assertions) when the dependency is absent, matching the guards the other
  integration tests already used.

## [0.1.0] — 2026-04-19

Initial alpha release. The plugin is feature-complete for the βyblos v1 launch
and is shipping alongside `local_byblos` 2026041700.

### Added
- Submission plugin scaffolding: `lib.php`, `locallib.php`, `version.php`, `settings.php`.
- Three submission modes — `snapshot_on_submit`, `live`, `live_until_locked` — with per-assignment configuration.
- Page-or-collection submission picker with ownership enforcement (students can only submit portfolios they own).
- Snapshot capture via `local_byblos\submission::capture_snapshot_if_needed()`:
  - Immediate capture on submit when in `snapshot_on_submit` mode.
  - Deferred capture on `lock()` when in `live_until_locked` mode.
- Peer review settings (enable, allocation mode, reviewers-per-student, visibility, scoring mode).
- Three peer allocation modes: manual, random, within-groups.
- Peer scoring modes: none (comments only), numeric (0–100), stars (1–5), rubric (uses assignment advanced grading).
- Peer-comment visibility staging: after reviewer submits / on grade release / teacher only.
- Assessment checklist (free-form per-assignment guidance shown to students).
- Submission summary HTML in the grading list, with snapshot-captured timestamp or live-reference label.
- "Open portfolio" button on the grading screen, plus a "Manage peer reviewers" link for graders when peer review is enabled.
- Static URL builder `assign_submission_byblos::url_for_assign_submission()` for embedding portfolios in iframes from external renderers.
- `copy_submission()` so new attempts inherit the previous portfolio choice.
- `remove()` so deleting an assignment submission cleans up the βyblos pointer.
- Privacy API provider declaring no personal data is stored locally (pointer-only model documented in `privacy:metadata`).
- Site-wide setting: "Enabled by default" for new assignments.
- 47 language strings covering the full UI surface.
- Plugin registration tests in `tests/plugin_test.php`.
- Event classes: `submission_created`, `submission_updated`, `snapshot_captured`, `peer_review_assigned`, `peer_review_completed`, `peer_comment_posted`.

### Changed
- N/A (initial release).

### Deprecated
- N/A.

### Removed
- N/A.

### Fixed
- N/A (initial release).

### Security
- All page/collection submissions verify ownership before persisting (`$page->userid === $USER->id`).
- Plugin stores no personal data; privacy provider explicitly declares this and points users to `local_byblos` for portfolio content.

[0.2.0]: https://github.com/sats-edu/moodle-assignsubmission_byblos/releases/tag/v0.2.0
[0.1.0]: https://github.com/sats-edu/moodle-assignsubmission_byblos/releases/tag/v0.1.0
