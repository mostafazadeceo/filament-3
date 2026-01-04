# Commerce Experience Specification

## Scope
Experience module provides reviews, ratings, Q&A, CSAT/NPS surveys, and buy-now preferences. It is tenant-scoped and permission-gated.

## Domain entities
- ExperienceReview, ExperienceReviewVote
- ExperienceQuestion, ExperienceAnswer
- ExperienceCsatSurvey, ExperienceCsatResponse
- ExperienceNpsSurvey, ExperienceNpsResponse
- ExperienceBuyNowPreference

## Key behaviors
- Reviews and Q&A support moderation states.
- CSAT surveys are dispatched via notify-core triggers.
- Buy-now preferences require explicit consent and optional 2FA.
