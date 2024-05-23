# Museum_Ticket_Reservation_Website
- Subject: CCS6344-DATABASE AND CLOUD SECURITY
- Lecturer Name:  Dr. Navaneethan A/L C. Arjuman
- Report Link: https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/blob/main/Group2_Assignment1.pdf
- Youtube Whole Workflow system: [https://youtu.be/8PvGNi1_FBA](https://youtu.be/4yXjNV-xZIM)

# Group 2 Member Details
1. Nur Ayu Amira Binti Idris 1201200722
2. Muhammad Dhiyaul Naufal bin Zainuddin 1201201537
3. Mannoj Sakthivel 1221303085

# Language of our System
1. HTML, CSS &  Javascript - Front End
2. PHP - Backend
3. PHPMailer - OTP

# Steps to run this system
1. Download the zip folder for this code. Then, extract the Zip Folder. Rename the folder as "museum".
2. Insert the Folder into this path of your Desktop : C:\xampp\htdocs
   ![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/2be169db-6e78-47ce-9f84-89cefde00207)

3. Open your XAMPP Control Panel and click START Apache Server and MySQL Server
   ![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/a3535ccc-dbec-4c67-b675-a64e1e71e21e)

4. Go to this link http://localhost/phpmyadmin/index.php?route=/server/databases and create database name "museum"
   ![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/251d6de8-4f9e-4ba9-b878-9aad7edb36e3)

5. Import the sql file from the folder "museum.sql".
   ![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/88253bad-73a5-4b0f-8fe7-c59af9570d95)

6. For testing the user, you can start with User Register Page http://localhost/museum/register.php
   
7. For testing the admin, you can start with Admin Register Page http://localhost/museum/admin_register.php

8. For file "forgotpwd.php" and "admin_forgotpwd.php" ypu need to do configuration Email in XAMPP. You can follow this Youtube Tutorial "https://www.youtube.com/watch?v=TvaKz3wwvWY"

# Security that we have implement in this System
1. Authentication and Authorization
2. Hashing - Ensures the database is compromised
3. Masking - Prevents unauthorized access.
4. Encryption - Remains secure and unreadable without the decryption key.
5. Input Validation - Ensures the requirement meets security and complexity requirements
6. Audit Logs - Check Potential Bruce Force Attack
7. Monitor Traffic - Prevent DDoS Attack
8. Session Management - Prevent Unauthorized Access
9. Input Sanitization - Prevent XSS (Cross-Site Scripting) attacks
10. Prepared Statements - Prevents SQL Injection attacks
11. OTP Generation - Using PHPMailer
12. PHPMailer Configuration - Using SMTP with proper authentication
13. Backup and Recovery - Ensures proper creation and storage of backup files.

# User Dashboard
![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/584b0e1b-eec5-4b6d-8742-19cdfdeae697)

# Admin Dashboard
![image](https://github.com/NurAyuAmira/Museum_Ticket_Booking_Website/assets/94117067/d89e8b58-b910-4c8d-97c2-94a0473a2013)

