
# Credit-Card-Processing

## Overview
This project provides a complete system to manage credit card payments. Users can securely log in via email OTP, make transactions, and monitor their credit usage in a responsive dashboard. It includes analytics features like daily spending trends, highest transactions, and filtering capabilities.

## Features

### 🔐 User Authentication
- Email-based OTP login (valid for 30 seconds)
- Secure session management

### 👤 User Dashboard
- Add new transactions (with description and amount)
- ₹100,000 credit limit per user
- View available balance
- Daily transaction summaries
- Track highest payment made today
- Light/Dark mode toggle
- Filter transactions by date or card number

### 📊 Analytics Tab
- Total amount spent (Today)
- Highest payment made today
- Chart of daily spending trends
- Back button to return to Dashboard
- User-specific data only

## 🚀 Deployment
### Step 1: Clone the repository
```bash
git clone https://github.com/Sunil-7795/Credit_Card_Processing.git

```
### Step 2: Move it to XAMPP
```bash
Place the folder inside your htdocs/ directory in the XAMPP installation.
```
### Step 3: Start Apache & MySQL
```bash
Launch XAMPP and start Apache and MySQL.
```
### Step 4: Set up the Database
&nbsp;&nbsp;1. Open phpMyAdmin

&nbsp;&nbsp;2. Create a database named:
```bash
credit_processing_system
```
&nbsp;&nbsp;3. Import the SQL file located at /database/credit_processing_system.sql
### Step 5: Access the App
&nbsp;&nbsp;Open your browser and go to:
```bash
http://localhost/credit-card-processing/register.html
``` 
## Workflow
**New User:**
Register → Receive OTP → Login → Dashboard

**Returning User:**
Enter Email → Get OTP → Dashboard

**Dashboard Workflow:** 
Add Transaction → View balance → Open Analytics → Check spending summary → Back to Dashboard → Logout
## 👨‍💻 About Me
An aspiring Full Stack Developer with a focus on secure and dynamic web applications. Passionate about combining frontend interactivity with backend logic to deliver smooth user experiences. Skilled in PHP, MySQL, Bootstrap, JavaScript, and modern dashboard design.
## Author 
- [@Sunil](https://github.com/Sunil-7795)
## License
This project is licensed under the
[MIT](https://choosealicense.com/licenses/mit/) License.

