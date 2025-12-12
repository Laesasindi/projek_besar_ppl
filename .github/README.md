# GitHub Actions Testing

## 🚀 Automated Testing for Weather App

Simple GitHub Actions workflow for automated testing when code is pushed to GitHub.

## 📋 Workflow Overview

### **Test Workflow** (`test.yml`)
- **Triggers:** Push to `main`/`develop`, Pull Requests
- **Features:**
  - PHP syntax validation
  - PHPUnit test execution
  - API function testing
  - Project structure validation

## 🛠️ Setup Instructions

### 1. Repository Setup
```bash
# Clone the repository
git clone <your-repo-url>
cd weather-app

# Install dependencies
composer install

# Run tests locally
vendor/bin/phpunit
```

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

## 🔄 How It Works

1. **Push code** to GitHub
2. **Automatic testing** starts
3. **Results** shown in GitHub Actions tab
4. **Green checkmark** = tests passed ✅
5. **Red X** = tests failed ❌

## 🧪 What Gets Tested

- ✅ PHP syntax validation
- ✅ PHPUnit tests execution  
- ✅ API functions loading
- ✅ Project structure validation

## 🚨 If Tests Fail

1. Check the Actions tab in GitHub
2. Look at the error messages
3. Fix the issues locally
4. Push again

## 🎯 Benefits

- **Automatic Testing:** Every push is tested
- **Early Bug Detection:** Catch errors before deployment
- **Team Confidence:** Know the code works

---

**Happy Coding! 🌤️**