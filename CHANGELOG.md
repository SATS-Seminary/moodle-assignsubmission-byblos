# Changelog

All notable changes to **assignsubmission_byblos** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[0.1.0]: https://github.com/sats-edu/moodle-assignsubmission_byblos/releases/tag/v0.1.0
