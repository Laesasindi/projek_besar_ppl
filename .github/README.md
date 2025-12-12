# GitHub Actions CI/CD Pipeline

## 🚀 Automated Testing & Deployment for Weather App

This repository includes comprehensive GitHub Actions workflows for automated testing, code quality checks, and team collaboration.

## 📋 Workflows Overview

### 1. **Main CI/CD Pipeline** (`ci.yml`)
- **Triggers:** Push to `main`/`develop`, Pull Requests
- **Features:**
  - Multi-version PHP testing (8.1, 8.2, 8.3)
  - PHPUnit test execution with coverage
  - Code style checking (PHP CS Fixer)
  - Static analysis (PHPStan)
  - Security vulnerability scanning
  - Frontend validation (HTML, CSS, JS)
  - API endpoint testing
  - Performance testing (Lighthouse)
  - Deployment package creation

### 2. **Team Collaboration Workflow** (`team-workflow.yml`)
- **Triggers:** All branch pushes, Pull Requests
- **Features:**
  - Quick validation for feature branches
  - Team member file structure checking
  - Full test suite for main branches
  - Code review assistance
  - Deployment readiness assessment

### 3. **Branch Protection & Notifications** (`branch-protection.yml`)
- **Triggers:** Main/develop pushes, Pull Requests
- **Features:**
  - Team member notification system
  - Merge conflict detection
  - Progress tracking for team features
  - Automated PR analysis and commenting

## 🛠️ Setup Instructions

### 1. Repository Setup
```bash
# Clone the repository
git clone <your-repo-url>
cd weather-app

# Install dependencies
composer install

# Run initial tests
composer test
```

### 2. Required Secrets (Optional)
Add these to your GitHub repository secrets if needed:
- `WEATHER_API_KEY` - OpenWeatherMap API key
- `CODECOV_TOKEN` - For coverage reporting (optional)

### 3. Branch Protection Rules
Configure these in GitHub Settings > Branches:
- Require pull request reviews before merging
- Require status checks to pass before merging
- Require branches to be up to date before merging
- Include administrators in restrictions

## 📊 Team Member Responsibilities

### Paskah - Search Feature
**Files to maintain:**
- `features/paskah_search.php`
- `assets/js/paskah_search.js`
- `assets/css/paskah_search.css`

### Riza - Geography Feature
**Files to maintain:**
- `features/riza_geography_enhanced.php`
- `assets/js/riza_geography.js`
- `assets/css/riza_geography.css`

### Sylvian - Weather Map Feature
**Files to maintain:**
- `features/sylvian_weather_map.php`
- `assets/js/sylvian_weather_map.js`
- `assets/css/sylvian_weather_map.css`

### Sindi - Team Leader & Core Features
**Files to maintain:**
- `index.php`
- `config.php`
- `process.php`
- `functions/*.php`
- `tests/*.php`

## 🔄 Workflow Process

### Feature Development
1. Create feature branch: `git checkout -b feature/your-feature-name`
2. Make changes and commit
3. Push branch: `git push origin feature/your-feature-name`
4. **Automatic:** Quick validation runs
5. Create Pull Request to `develop`
6. **Automatic:** Full code review and testing
7. Merge after approval

### Release Process
1. Merge `develop` to `main`
2. **Automatic:** Full CI/CD pipeline runs
3. **Automatic:** Deployment package created
4. **Automatic:** Performance and security checks
5. **Manual:** Deploy to production server

## 📈 Quality Gates

### ✅ All commits must pass:
- PHP syntax validation
- PHPUnit tests (minimum 80% coverage)
- Code style standards (PSR-12)
- Static analysis (PHPStan level 5)
- Security vulnerability checks

### ✅ Pull Requests must pass:
- All CI checks
- Code review from team member
- No merge conflicts
- Documentation updates (if needed)

## 🚨 Troubleshooting

### Common Issues:

**1. PHP Syntax Errors**
```bash
# Check locally before pushing
find . -name "*.php" -exec php -l {} \;
```

**2. Test Failures**
```bash
# Run tests locally
composer test

# Run with coverage
composer test-coverage
```

**3. Code Style Issues**
```bash
# Fix automatically
composer cs-fix

# Check without fixing
composer cs-check
```

**4. Static Analysis Errors**
```bash
# Run PHPStan
composer stan
```

## 📞 Support

If you encounter issues with the CI/CD pipeline:
1. Check the Actions tab in GitHub for detailed logs
2. Review this documentation
3. Contact the team leader (Sindi)
4. Create an issue in the repository

## 🎯 Benefits

- **Automated Quality Assurance:** Every code change is automatically tested
- **Team Coordination:** Clear visibility of who's working on what
- **Deployment Safety:** Multiple checks before code reaches production
- **Code Consistency:** Automated formatting and style checking
- **Performance Monitoring:** Lighthouse checks ensure good user experience
- **Security:** Automated vulnerability scanning

---

**Happy Coding! 🌤️**