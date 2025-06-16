# Contributing to FixIt

Thank you for your interest in contributing to **FixIt**, our Laravel package for intelligent error logging and diagnostics. Whether you're fixing bugs, writing tests, or suggesting ideas — you're welcome here.

## 📦 Clone the repository

Always start from the `main` branch:

```bash
git clone https://github.com/coedevtech/fixit.git
cd fixit
git checkout main
```

> 🚨 **Important:** All pull requests must target the `main` branch.

## 🌱 Create a feature or fix branch

Please create a new branch for your change:

```bash
git checkout -b feat/add-ai-suggestions
# or
git checkout -b fix/encryption-handling
```

Use clear, semantic names (`feat/`, `fix/`, `chore/`, etc.).

## ✅ Pull request guidelines

- ✅ Submit all PRs to the `main` branch
- ✅ Use [Conventional Commit](https://www.conventionalcommits.org/) format in your PR title
- ✅ Include relevant tests for new functionality
- ✅ Keep PRs focused and avoid bundling unrelated changes

## 🧪 Running tests

Install dependencies and run Pest:

```bash
composer install
./vendor/bin/pest
```

## ✨ Code style

We follow **PSR-12** and recommend using Laravel Pint:

```bash
composer require laravel/pint --dev
vendor/bin/pint
```

## 🙌 Questions or support?

Open an issue or start a discussion — we're happy to help.
