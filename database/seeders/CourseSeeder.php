<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $courses = [
            ['code' => 'ABC', 'name' => 'BACHELOR OF ARTS IN MASS COMMUNICATION'],
            ['code' => 'BECE', 'name' => 'BACHELOR OF EARLY CHILDHOOD EDUCATION'],
            ['code' => 'BEED', 'name' => 'BACHELOR OF ELEMENTARY EDUCATION'],
            ['code' => 'BEED/ECE', 'name' => 'BACHELOR OF ELEMENTARY EDUCATION MAJOR IN EARLY CHILDHOOD EDUCATION'],
            ['code' => 'BEED/MGE', 'name' => 'BACHELOR OF ELEMENTARY EDUCATION MAJOR IN GENERAL EDUCATION'],
            ['code' => 'BEED/MSE', 'name' => 'BACHELOR OF ELEMENTARY EDUCATION MAJOR IN SPECIAL EDUCATION'],
            ['code' => 'BSA', 'name' => 'BACHELOR OF SCIENCE IN ACCOUNTANCY'],
            ['code' => 'BSBA/FM', 'name' => 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN FINANCIAL MANAGEMENT'],
            ['code' => 'BSBA/MA', 'name' => 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MANAGEMENT ACCOUNTING'],
            ['code' => 'BSBA/MM', 'name' => 'BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION MAJOR IN MARKETING MANAGEMENT'],
            ['code' => 'BSCE', 'name' => 'BACHELOR OF SCIENCE IN CIVIL ENGINEERING'],
            ['code' => 'BSCPE', 'name' => 'BACHELOR OF SCIENCE IN COMPUTER ENGINEERING'],
            ['code' => 'BSCS', 'name' => 'BACHELOR OF SCIENCE IN COMPUTER SCIENCE'],
            ['code' => 'BSCS/DS', 'name' => 'BACHELOR OF SCIENCE IN COMPUTER SCIENCE MAJOR IN DATA SCIENCE'],
            ['code' => 'SOC', 'name' => 'BACHELOR OF SCIENCE IN CRIMINOLOGY'],
            ['code' => 'BSEE', 'name' => 'BACHELOR OF SCIENCE IN ELECTRICAL ENGINEERING'],
            ['code' => 'BSECE', 'name' => 'BACHELOR OF SCIENCE IN ELECTRONIC ENGINEERING'],
            ['code' => 'BSHM', 'name' => 'BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT'],
            ['code' => 'BSIT', 'name' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY'],
            ['code' => 'BSIT/M', 'name' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY MAJOR IN MULTIMEDIA ARTS AND ANIMATION'],
            ['code' => 'BSIT/N', 'name' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY MAJOR IN NETWORK INFRASTRUCTURE WITH CYBERSECURITY'],
            ['code' => 'BSIT/W', 'name' => 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY MAJOR IN WEB AND MOBILE APP DEVELOPMENT'],
            ['code' => 'BSME', 'name' => 'BACHELOR OF SCIENCE IN MECHANICAL ENGINEERING'],
            ['code' => 'BSN', 'name' => 'BACHELOR OF SCIENCE IN NURSING'],
            ['code' => 'BSPSYCH', 'name' => 'BACHELOR OF SCIENCE IN PSYCHOLOGY'],
            ['code' => 'BSTM', 'name' => 'BACHELOR OF SCIENCE IN TOURISM MANAGEMENT'],
            ['code' => 'BSED/ENGLISH', 'name' => 'BACHELOR OF SECONDARY EDUCATION MAJOR IN ENGLISH'],
            ['code' => 'BSED/FILIPINO', 'name' => 'BACHELOR OF SECONDARY EDUCATION MAJOR IN FILIPINO'],
            ['code' => 'BSED/MATHEMATICS', 'name' => 'BACHELOR OF SECONDARY EDUCATION MAJOR IN MATHEMATICS'],
            ['code' => 'BSED/SCIENCE', 'name' => 'BACHELOR OF SECONDARY EDUCATION MAJOR IN SCIENCE'],
            ['code' => 'BSSPED', 'name' => 'BACHELOR OF SPECIAL NEEDS EDUCATION:GENERALIST'],
            ['code' => 'DBAD', 'name' => 'DOCTOR IN BUSINESS ADMINISTRATION'],
            ['code' => 'PHD /EDUCATIONAL LEADERSHIP AND MANAGEMENT', 'name' => 'DOCTOR OF PHILOSOPHY EDUCATIONAL LEADERSHIP AND MANAGEMENT'],
            ['code' => 'MBA', 'name' => 'MASTER IN BUSINESS ADMINISTRATION'],
            ['code' => 'MED /EARLY CHILDHOOD EDUCATION', 'name' => 'MASTER IN EDUCATION MAJOR IN EARLY CHILDHOOD EDUCATION'],
            ['code' => 'MED /EDUCATIONAL LEADERSHIP', 'name' => 'MASTER IN EDUCATION MAJOR IN EDUCATIONAL LEADERSHIP'],
            ['code' => 'MED /ENGLISH LANGUAGE TEACHING', 'name' => 'MASTER IN EDUCATION MAJOR IN ENGLISH LANGUAGE TEACHING'],
            ['code' => 'MED /FILIPINO', 'name' => 'MASTER IN EDUCATION MAJOR IN FILIPINO'],
            ['code' => 'MED /GENERAL SCIENCE', 'name' => 'MASTER IN EDUCATION MAJOR IN GENERAL SCIENCE'],
            ['code' => 'MED /MATHEMATICS', 'name' => 'MASTER IN EDUCATION MAJOR IN MATHEMATICS'],
            ['code' => 'MED /SPECIAL EDUCATION', 'name' => 'MASTER IN EDUCATION MAJOR IN SPECIAL EDUCATION'],
            ['code' => 'MIT', 'name' => 'MASTER IN INFORMATION TECHNOLOGY'],
            ['code' => 'MAED /EDUCATIONAL MANAGEMENT', 'name' => 'MASTER OF ARTS IN EDUCATION MAJOR IN EDUCATIONAL MANAGEMENT'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
