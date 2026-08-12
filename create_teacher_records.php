<?php
// Script to create teacher records for users with role_id = 3
// Run this script once to create missing teacher records

require_once 'vendor/autoload.php';

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

// Load configuration
Configure::config('default', new PhpConfig());
Configure::load('app', 'default', false);

// Set up database connection
$config = Configure::read('Datasources.default');
ConnectionManager::setConfig('default', $config);

try {
    $usersTable = TableRegistry::get('Users');
    $teachersTable = TableRegistry::get('Teachers');
    
    // Find all users with role_id = 3 (teachers)
    $teacherUsers = $usersTable->find()
        ->where(['role_id' => 3])
        ->toArray();
    
    echo "Found " . count($teacherUsers) . " users with teacher role.\n";
    
    $created = 0;
    $skipped = 0;
    
    foreach ($teacherUsers as $user) {
        // Check if teacher record already exists
        $existingTeacher = $teachersTable->find()
            ->where(['user_id' => $user->id])
            ->first();
            
        if ($existingTeacher) {
            echo "Teacher record already exists for user ID: " . $user->id . " (" . $user->fname . " " . $user->lname . ")\n";
            $skipped++;
            continue;
        }
        
        // Create new teacher record
        $teacher = $teachersTable->newEmptyEntity();
        $teacher->user_id = $user->id;
        $teacher->firstname = $user->fname;
        $teacher->lastname = $user->lname;
        $teacher->middlename = $user->mname;
        $teacher->gender = 'Male'; // Default
        $teacher->date_created = new \DateTime();
        $teacher->department_id = 1; // Default department
        $teacher->staffgrade_id = 1; // Default staff grade
        $teacher->staffdepartment_id = 1; // Default staff department
        $teacher->profile = 'Teacher profile information';
        $teacher->passport = '';
        $teacher->cv = '';
        
        if ($teachersTable->save($teacher)) {
            echo "Created teacher record for user ID: " . $user->id . " (" . $user->fname . " " . $user->lname . ")\n";
            $created++;
        } else {
            echo "Failed to create teacher record for user ID: " . $user->id . "\n";
        }
    }
    
    echo "\nSummary:\n";
    echo "Created: " . $created . " teacher records\n";
    echo "Skipped: " . $skipped . " (already existed)\n";
    echo "Total processed: " . count($teacherUsers) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
