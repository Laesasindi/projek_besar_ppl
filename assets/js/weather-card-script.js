/**
 * Modern Weather Dashboard JavaScript
 * Handles the new purple gradient weather interface
 */

// Global variables
let currentCity = 'Jakarta';
let weatherData = {};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    loadWeatherData();
    setupEventListeners();
    updateDateTime();
    
    // Update time every minute
    setInterval(updateDateTime, 60000);
});

// Initialize the application
function initializeApp() {
    console.log('Weather Dashboard initialized');
    
    // Set default weather data
    setDefaultWeatherData();
    
    // Load weather for multiple cities
    loadMultipleCitiesWeather();
}

// Set default weather data for immediate display
function setDefaultWeatherData() {
    const defaultData = {
        Jakarta: {
            temp: 28,
            condition: 'Cloudy',
            feelsLike: 31,
            visibility: '10 km',
            pressure: '1015 hPa',
            uvIndex: 4,
            icon: 'partly-cloudy.svg',
            hourly: [
                { time: '2 PM', temp: 29, icon: 'partly-cloudy.svg' },
                { time: '3 PM', temp: 28, icon: 'partly-cloudy.svg' },
                { time: '4 PM', temp: 27, icon: 'cloudy.svg' },
                { time: '5 PM', temp: 26, icon: 'rainy.svg' }
            ]
        },
        Bandung: { temp: 26, condition: 'Partly Cloudy', icon: 'partly-cloudy.svg' },
        Medan: { temp: 24, condition: 'Rainy', icon: 'rainy.svg' },
        Semarang: { temp: 25, condition: 'Sunny', icon: 'sunny.svg' },
        Makassar: { temp: 28, condition: 'Cloudy', icon: 'cloudy.svg' },
        Surabaya: { temp: 28, condition: 'Cloudy', icon: 'cloudy.svg' }
    };
    
    weatherData = defaultData;
    updateMainWeatherDisplay(defaultData.Jakarta);
}

// Update main weather display
function updateMainWeatherDisplay(data) {
    // Update main temperature and condition
    document.querySelector('.main-temp').textContent = data.temp + '°';
    document.querySelector('.condition').textContent = data.condition;
    document.querySelector('.feels-like span').textContent = data.feelsLike + '°';
    
    // Update weather icon
    const mainIcon = document.querySelector('.weather-icon-main img');
    if (mainIcon) {
        mainIcon.src = `assets/images/weather/${data.icon}`;
        mainIcon.alt = data.condition;
    }
    
    // Update weather details
    if (data.visibility) document.querySelector('.weather-details .detail-item:nth-child(1) .detail-value').textContent = data.visibility;
    if (data.pressure) document.querySelector('.weather-details .detail-item:nth-child(2) .detail-value').textContent = data.pressure;
    if (data.uvIndex) document.querySelector('.weather-details .detail-item:nth-child(3) .detail-value').textContent = data.uvIndex;
    
    // Update hourly forecast if available
    if (data.hourly) {
        updateHourlyForecast(data.hourly);
    }
}

// Update hourly forecast
function updateHourlyForecast(hourlyData) {
    const hourlyContainer = document.querySelector('.hourly-items');
    if (!hourlyContainer) return;
    
    hourlyContainer.innerHTML = '';
    
    hourlyData.forEach(hour => {
        const hourlyItem = document.createElement('div');
        hourlyItem.className = 'hourly-item';
        hourlyItem.innerHTML = `
            <img src="assets/images/weather/${hour.icon}" alt="Weather">
            <span class="hourly-temp">${hour.temp}°</span>
            <span class="hourly-time">${hour.time}</span>
        `;
        hourlyContainer.appendChild(hourlyItem);
    });
}

// Update date and time
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    
    const dateString = now.toLocaleDateString('en-US', options);
    const dateElement = document.querySelector('.date-time');
    if (dateElement) {
        dateElement.textContent = dateString;
    }
}

// Setup event listeners
function setupEventListeners() {
    // Navigation items
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav items
            navItems.forEach(nav => nav.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Handle section switching (for future implementation)
            const section = this.getAttribute('data-section');
            handleSectionSwitch(section);
        });
    });
    
    // Forecast items click handlers
    const forecastItems = document.querySelectorAll('.forecast-item');
    forecastItems.forEach(item => {
        item.addEventListener('click', function() {
            const cityName = this.querySelector('.forecast-city').textContent;
            switchToCity(cityName);
        });
    });
}

// Handle section switching
function handleSectionSwitch(section) {
    console.log(`Switching to section: ${section}`);
    
    switch(section) {
        case 'home':
            // Already on home, maybe refresh data
            loadWeatherData();
            break;
        case 'search':
            showSearchInterface();
            break;
        case 'map':
            showWeatherMap();
            break;
        case 'geography':
            showGeographyInterface();
            break;
        default:
            console.log('Unknown section:', section);
    }
}

// Switch to different city
function switchToCity(cityName) {
    currentCity = cityName;
    
    // Update city name display
    document.querySelector('.city-name').textContent = cityName;
    
    // Load weather data for the selected city
    if (weatherData[cityName]) {
        updateMainWeatherDisplay(weatherData[cityName]);
    } else {
        loadCityWeather(cityName);
    }
    
    // Add visual feedback
    const cityElement = document.querySelector('.city-name');
    cityElement.style.transform = 'scale(1.05)';
    setTimeout(() => {
        cityElement.style.transform = 'scale(1)';
    }, 200);
}

// Load weather data from API
async function loadWeatherData() {
    try {
        // This would typically make an API call
        // For now, we'll use the default data
        console.log('Loading weather data for:', currentCity);
        
        // Simulate API call delay
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Update with current city data
        if (weatherData[currentCity]) {
            updateMainWeatherDisplay(weatherData[currentCity]);
        }
        
    } catch (error) {
        console.error('Error loading weather data:', error);
        showErrorMessage('Failed to load weather data');
    }
}

// Load weather for multiple cities
async function loadMultipleCitiesWeather() {
    const cities = ['Bandung', 'Medan', 'Semarang', 'Makassar', 'Surabaya'];
    
    // This would typically make API calls for each city
    // For now, we'll use the default data
    console.log('Loading weather for multiple cities');
}

// Load weather for specific city
async function loadCityWeather(cityName) {
    try {
        console.log(`Loading weather for ${cityName}`);
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 300));
        
        // For demo purposes, generate some weather data
        const demoData = generateDemoWeatherData(cityName);
        weatherData[cityName] = demoData;
        
        updateMainWeatherDisplay(demoData);
        
    } catch (error) {
        console.error(`Error loading weather for ${cityName}:`, error);
        showErrorMessage(`Failed to load weather for ${cityName}`);
    }
}

// Generate demo weather data
function generateDemoWeatherData(cityName) {
    const conditions = ['Sunny', 'Cloudy', 'Partly Cloudy', 'Rainy', 'Overcast'];
    const icons = ['sunny.svg', 'cloudy.svg', 'partly-cloudy.svg', 'rainy.svg', 'overcast.svg'];
    
    const randomCondition = conditions[Math.floor(Math.random() * conditions.length)];
    const randomIcon = icons[Math.floor(Math.random() * icons.length)];
    const randomTemp = Math.floor(Math.random() * 15) + 20; // 20-35°C
    
    return {
        temp: randomTemp,
        condition: randomCondition,
        feelsLike: randomTemp + Math.floor(Math.random() * 5),
        visibility: '10 km',
        pressure: '1015 hPa',
        uvIndex: Math.floor(Math.random() * 10),
        icon: randomIcon,
        hourly: generateHourlyData()
    };
}

// Generate hourly data
function generateHourlyData() {
    const hours = ['2 PM', '3 PM', '4 PM', '5 PM', '6 PM'];
    const icons = ['sunny.svg', 'cloudy.svg', 'partly-cloudy.svg', 'rainy.svg'];
    
    return hours.map(hour => ({
        time: hour,
        temp: Math.floor(Math.random() * 10) + 25,
        icon: icons[Math.floor(Math.random() * icons.length)]
    }));
}

// Show search interface
function showSearchInterface() {
    // This would show a search modal or interface
    const searchQuery = prompt('Enter city name to search:');
    if (searchQuery && searchQuery.trim()) {
        switchToCity(searchQuery.trim());
    }
}

// Show weather map
function showWeatherMap() {
    alert('Weather Map feature coming soon!');
}

// Show geography interface
function showGeographyInterface() {
    alert('Geography feature coming soon!');
}

// Show error message
function showErrorMessage(message) {
    // Create a temporary error notification
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-notification';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(255, 0, 0, 0.8);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(errorDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        errorDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(errorDiv);
        }, 300);
    }, 3000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Utility functions
function formatTemperature(temp) {
    return Math.round(temp) + '°';
}

function formatTime(date) {
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}

// Export functions for global access
window.switchToCity = switchToCity;
window.loadWeatherData = loadWeatherData;