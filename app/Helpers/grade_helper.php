<?php

/*
@return string grade
examples
$grade = getGrade(); // output : Bronze-1
$grade = getGrade('S'); // output : Diamond
*/
function getGrade($grade = 'E') {
    $grade = strtoupper($grade);
    $grades = [
        'S' => 'Diamond',
        'A' => 'Platinum',
        'B' => 'Gold',
        'C' => 'Silver',
        'D' => 'Bronze',
        'E' => 'Bronze-1',
    ];
    if(isset($grades[$grade])) return $grades[$grade];
    else return $grade;
}

/*
@return string grade
examples
$grade = getGradeDefinition(); // output : Totally damage
$grade = getGradeDefinition('S'); // output : Like new, no damage point
*/
function getGradeDefinition($grade = 'E') {
    $grade = strtoupper($grade);
    $grades = [
        'S' => 'Like new, no damage point',
        'A' => 'Like new, has 1 damage point',
        'B' => 'Has 2-3 damage points',
        'C' => 'Has 4-7 damage points',
        'D' => 'Has 7 or more damage points',
        'E' => 'Totally damage',
    ];
    if(isset($grades[$grade])) return $grades[$grade];
    else return $grade;
}
