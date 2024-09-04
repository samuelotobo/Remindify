// quotes.js
const quotes = [
    "Believe you can and you're halfway there. — Theodore Roosevelt",
    "Do what you can, with what you have, where you are. — Theodore Roosevelt",
    "Success is not final, failure is not fatal: It is the courage to continue that counts. — Winston Churchill",
    "You are never too old to set another goal or to dream a new dream. — C.S. Lewis",
    "Start where you are. Use what you have. Do what you can. — Arthur Ashe",
    "Act as if what you do makes a difference. It does. — William James",
    "Success usually comes to those who are too busy to be looking for it. — Henry David Thoreau",
    "Don’t watch the clock; do what it does. Keep going. — Sam Levenson",
    "The future depends on what you do today. — Mahatma Gandhi",
    "The only limit to our realization of tomorrow is our doubts of today. — Franklin D. Roosevelt"
];

// Function to display a new quote each day
function displayDailyQuote() {
    const today = new Date();
    const dayIndex = today.getDate() % quotes.length;
    const dailyQuote = quotes[dayIndex];

    document.getElementById('daily-quote').textContent = dailyQuote;
}

// Run the function on page load
document.addEventListener('DOMContentLoaded', displayDailyQuote);
