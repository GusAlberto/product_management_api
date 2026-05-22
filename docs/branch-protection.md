# Branch Protection Guidance

These settings must be configured in GitHub repository settings for `main` (and ideally `develop`):

1. Require a pull request before merging.
2. Require at least 1 approval before merge.
3. Require status checks to pass before merge.
4. Require conversation resolution before merge.
5. Block force pushes.
6. Restrict branch deletion.
7. Require code owner review if CODEOWNERS is used.

Recommended required status checks:

- Secret Scan
- Dependency Audit
- Feature Test Suite

Recommended repository hygiene:

- Keep `.env` out of the repository.
- Rotate secrets immediately if a leak is detected.
- Re-clone after history rewrites.