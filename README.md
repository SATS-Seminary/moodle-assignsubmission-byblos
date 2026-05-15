# βyblos Portfolio — Assignment Submission Plugin

![Moodle 5.0+](https://img.shields.io/badge/Moodle-5.0%2B-orange) ![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-blue) ![Maturity Alpha](https://img.shields.io/badge/maturity-alpha-yellow) ![License GPLv3](https://img.shields.io/badge/license-GPLv3-green)

**assignsubmission_byblos** lets students submit a [βyblos](../local_byblos/README.md) portfolio page or collection as a `mod_assign` submission. Teachers grade portfolio work directly inside Moodle's standard grading workflow, with a choice of live or snapshot semantics, optional peer review, and rubric-driven scoring.

This plugin is the assignment-side companion to `local_byblos` — install both together to enable portfolio-based assessment.

## What it does

- **Three submission modes** — pick the semantics that fit the assessment:
  - **Snapshot on submit**: an immutable copy of the portfolio is captured at submit time. The teacher grades the frozen version, even if the student keeps editing.
  - **Live**: the teacher always sees the current state of the student's page. The student can keep editing during grading.
  - **Live until locked**: behaves as live until submissions are locked (e.g. after the deadline), at which point the system freezes a snapshot automatically.
- **Page or collection submissions** — students can submit a single page or a whole multi-page collection. The accepted unit is configurable per assignment.
- **Peer review** — optionally require students to review each other's submissions, with three allocation modes:
  - **Manual**: the teacher assigns reviewers individually.
  - **Random**: the system distributes reviewers across submissions, configurable reviewers-per-student.
  - **Within groups**: reviewers are drawn from the student's Moodle group.
- **Advanced grading rubric integration** — peer review can use Moodle's advanced-grading rubric component. Reviewers see the criterion × level grid and the advisory score is computed from selected levels. Numeric, star (1–5), and comments-only modes are also available.
- **Inline section comments** — teachers and peers leave comments anchored to a specific section of the portfolio. Visibility is controllable (after reviewer submits / on grade release / teacher only) so peer feedback can be staged.
- **Collection-level submissions** — submitting a collection captures every member page in a single snapshot, so a multi-page mini-site can be assessed as one artefact.
- **Moodle file API integration** — all portfolio media is managed by `local_byblos` through Moodle's file API; no external storage is needed.
- **Assessment checklist** — teachers can attach a free-form checklist that students see while editing.
- **New-attempt support** — when mod_assign opens a new attempt, the previous portfolio choice is copied forward so students can iterate.

## Requirements

- Moodle 5.0+ (build 2024100700 or later)
- PHP 8.1+
- [`local_byblos`](../local_byblos/) v2026041700 or later (declared as a hard dependency)

## Installation

1. Make sure `local_byblos` is installed first — `assignsubmission_byblos` will refuse to install without it.
2. Copy the `assignsubmission_byblos` folder to `{moodleroot}/mod/assign/submission/byblos/`.
3. Visit **Site administration → Notifications** to trigger the install.
4. Enable the submission type per assignment under **Submission types → Byblos portfolio**.

The plugin's component name is `assignsubmission_byblos`, so its install path is `mod/assign/submission/byblos/` — the standard location for an `assignsubmission_*` subplugin of `mod_assign`.

## Configuration

### Site-wide (Site administration → Plugins → Assignment plugins → Byblos portfolio)

| Setting | Default | Description |
|---------|---------|-------------|
| Enabled by default | No | If on, every new assignment has the Byblos submission type pre-enabled |

### Per-assignment (Edit assignment → Submission types → Byblos portfolio)

| Setting | Options | Notes |
|---------|---------|-------|
| Accepted submission type | Page or collection / Single page / Collection | Restricts what students can submit |
| Snapshot mode | Snapshot on submit / Live / Live until locked | See "What it does" above |
| Enable peer review | On / Off | Turns on peer-review allocation and visibility settings |
| Peer assignment mode | Manual / Random / Within groups | How reviewers are allocated |
| Reviewers per student | 2 (default) | Random mode only |
| Peer comments visible to reviewee | After reviewer submits / On grade release / Teacher only | Controls staging of peer feedback |
| Peer scoring mode | None / Numeric / Stars / Rubric | Rubric uses the assignment's advanced-grading rubric |
| Assessment checklist | Free text, one item per line | Shown to students while editing; not enforced |

## Capabilities

This plugin reuses Moodle's standard `mod/assign` capabilities — it does not introduce its own.

| Capability | Used for |
|------------|----------|
| `mod/assign:submit` | Submit a portfolio as the student |
| `mod/assign:grade` | View and grade portfolios; access peer-review management |
| `local/byblos:viewshared` | Provided by `local_byblos`; required for graders to view portfolios shared with them |

## Privacy & GDPR

`assignsubmission_byblos` does not store personal data of its own. It stores a pointer (page id or collection id) to a portfolio managed by `local_byblos`, which is the system of record for portfolio content and implements the privacy API across all its tables. When the assignment submission is deleted, the pointer is removed; the underlying portfolio remains the property of the student under `local_byblos`.

## Events

This plugin fires the following events for activity logging and analytics:

- `\assignsubmission_byblos\event\submission_created` — a student first selects a portfolio for the assignment.
- `\assignsubmission_byblos\event\submission_updated` — the student changes which page or collection is submitted.
- `\assignsubmission_byblos\event\snapshot_captured` — a snapshot is taken (snapshot-on-submit or on lock).
- `\assignsubmission_byblos\event\peer_review_assigned` — a peer reviewer is allocated.
- `\assignsubmission_byblos\event\peer_review_completed` — a peer marks their review complete.
- `\assignsubmission_byblos\event\peer_comment_posted` — a peer adds an inline comment.

## Roadmap

- [ ] Auto-generate group collections when a `mod_assign` group assignment uses this submission type
- [ ] Bulk download of submitted portfolios as PDF
- [ ] Calibrated peer review (reviewer reliability scoring)
- [ ] Webhooks on snapshot capture for downstream analytics
- [ ] Import existing rubric criteria from a previous assignment

## License

GPL v3 — see the [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.html), the same license as Moodle itself.

## Credits

Developed by the South African Theological Seminary (SATS) IT team.
