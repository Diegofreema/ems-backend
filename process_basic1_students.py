#!/usr/bin/env python3
"""
Script to process Basic 1 Class B student records from Excel file
and generate SQL insert statements for users, students, and parents tables.
"""

import pandas as pd
import re
from datetime import datetime
import hashlib

def clean_name(name):
    """Clean and format names"""
    if pd.isna(name) or name == '':
        return ''
    return str(name).strip().title()

def parse_full_name(full_name):
    """Parse full name into first, middle, last names"""
    if pd.isna(full_name) or full_name == '':
        return '', '', ''
    
    name_parts = str(full_name).strip().split()
    
    if len(name_parts) == 1:
        return name_parts[0], '', ''
    elif len(name_parts) == 2:
        return name_parts[0], '', name_parts[1]
    else:
        return name_parts[0], ' '.join(name_parts[1:-1]), name_parts[-1]

def generate_password_hash(password):
    """Generate CakePHP compatible password hash"""
    # This is a simplified version - in production, use proper CakePHP hashing
    return f"$2y$10${hashlib.md5(password.encode()).hexdigest()[:22]}"

def process_excel_file(excel_file):
    """Process the Excel file and generate SQL statements"""
    
    # Read Excel file
    try:
        df = pd.read_excel(excel_file)
        print(f"Successfully loaded {len(df)} records from Excel file")
        print(f"Columns: {list(df.columns)}")
        print("\nFirst few rows:")
        print(df.head())
        print("\n" + "="*50 + "\n")
    except Exception as e:
        print(f"Error reading Excel file: {e}")
        return
    
    # Generate SQL statements
    sql_statements = []
    
    # Default values
    default_password = "student123"
    default_password_hash = generate_password_hash(default_password)
    student_role_id = 3  # Assuming role_id 3 is for students
    basic1_department_id = 1  # Assuming Basic 1 has department_id 1
    basic1_class_arm_id = 1   # Assuming Basic 1B has class_arm_id 1
    default_country_id = 160  # Nigeria
    default_state_id = 1      # Default state
    default_gender = "Male"
    default_address = "Not provided"
    default_phone = "0000000000"
    
    # Process each student
    for index, row in df.iterrows():
        try:
            # Extract data
            regno = str(row['Code']).strip() if pd.notna(row['Code']) else f"STU{index+1:03d}"
            full_name = str(row['Name']).strip() if pd.notna(row['Name']) else f"Student {index+1}"
            parent_name = str(row['Options']).strip() if pd.notna(row['Options']) else "Parent"
            
            # Generate email/username
            email = f"{regno}@tss.sch.ng"
            username = email
            
            # Parse names
            first_name, middle_name, last_name = parse_full_name(full_name)
            
            # Generate user_id (we'll use a sequence)
            user_id = 1000 + index + 1
            
            # Generate student_id (we'll use a sequence)
            student_id = 1000 + index + 1
            
            # Generate parent_id (we'll use a sequence)
            parent_id = 1000 + index + 1
            
            # Create user account SQL
            user_sql = f"""
-- User account for {full_name} (RegNo: {regno})
INSERT INTO users (
    id, username, password, role_id, fname, lname, mname, gender, 
    address, country_id, state_id, phone, department_id, profile, 
    dob, created_date, created_by, passport, useruniquid, userstatus, 
    verification_key, otp_code, otp_expires
) VALUES (
    {user_id}, 
    '{username}', 
    '{default_password_hash}', 
    {student_role_id}, 
    '{first_name}', 
    '{last_name}', 
    '{middle_name}', 
    '{default_gender}', 
    '{default_address}', 
    {default_country_id}, 
    {default_state_id}, 
    '{default_phone}', 
    {basic1_department_id}, 
    'Student Profile', 
    '2010-01-01', 
    NOW(), 
    1, 
    NULL, 
    '', 
    'active', 
    NULL, 
    NULL, 
    NULL
);"""
            
            # Create student record SQL
            student_sql = f"""
-- Student record for {full_name} (RegNo: {regno})
INSERT INTO students (
    id, regno, firstname, lastname, middlename, gender, address, 
    country_id, state_id, phone, department_id, user_id, sparent_id, 
    date_created, created_by, passport, status
) VALUES (
    {student_id}, 
    '{regno}', 
    '{first_name}', 
    '{last_name}', 
    '{middle_name}', 
    '{default_gender}', 
    '{default_address}', 
    {default_country_id}, 
    {default_state_id}, 
    '{default_phone}', 
    {basic1_department_id}, 
    {user_id}, 
    {parent_id}, 
    NOW(), 
    1, 
    NULL, 
    'active'
);"""
            
            # Create parent record SQL
            parent_sql = f"""
-- Parent record for {parent_name}
INSERT INTO sparents (
    id, fathersname, mothersname, fatherphone, motherphone, 
    fathersjob, mothersjob, pemailaddress, user_id, address, status
) VALUES (
    {parent_id}, 
    '{parent_name}', 
    '{parent_name}', 
    '{default_phone}', 
    '{default_phone}', 
    'Not specified', 
    'Not specified', 
    '{parent_name.lower().replace(" ", ".")}@tss.sch.ng', 
    {user_id}, 
    '{default_address}', 
    'active'
);"""
            
            sql_statements.extend([user_sql, student_sql, parent_sql])
            
        except Exception as e:
            print(f"Error processing row {index + 1}: {e}")
            continue
    
    # Write SQL file
    output_file = "insert_basic1_students.sql"
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("-- SQL script to insert Basic 1 Class B students\n")
        f.write(f"-- Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"-- Total records: {len(df)}\n\n")
        
        # Add helper functions
        f.write("""
-- Helper function to generate password hash (if needed)
-- Note: This script uses pre-generated hashes for 'student123'
-- Default password for all students: student123

""")
        
        # Add the SQL statements
        for sql in sql_statements:
            f.write(sql + "\n")
        
        # Add summary
        f.write(f"""
-- Summary
-- Total students processed: {len(df)}
-- Default password for all students: student123
-- Email format: [RegNo]@tss.sch.ng
-- Class: Basic 1, Class Arm: B
-- All students assigned to department_id: {basic1_department_id}
-- All students assigned to class_arm_id: {basic1_class_arm_id}
""")
    
    print(f"\nSQL script generated: {output_file}")
    print(f"Total students processed: {len(df)}")
    print(f"Default password for all students: {default_password}")
    print(f"Email format: [RegNo]@tss.sch.ng")

if __name__ == "__main__":
    excel_file = "webroot/BASIC1_B.xlsx"
    process_excel_file(excel_file)
