<?php

require_once __DIR__ . '/../api/models/databaseService.php';
require_once __DIR__ . '/../vendor/autoload.php';

function testConnection() {
    $dbService = new DatabaseService(null);
    if ($dbService->GetConnection() === null) {
        echo "FAIL: Could not establish database connection\n";
        return false;
    }
    echo "PASS: Database connection established\n";
    return true;
}

function testQuery() {
    $dbService = new DatabaseService(null);
    $result = $dbService->Query("SELECT 1 as test");
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Basic query test failed\n";
        return false;
    }
    $row = $result->fetch_assoc();
    if ($row['test'] != 1) {
        echo "FAIL: Query returned unexpected result\n";
        return false;
    }
    echo "PASS: Basic query test succeeded\n";
    return true;
}

function testFindUserByUsername() {
    $dbService = new DatabaseService(null);
    $result = $dbService->Query("SELECT * FROM Users WHERE username = 'alice'");
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find user 'alice'\n";
        return false;
    }
    $user = $result->fetch_assoc();
    if ($user['email'] !== 'alice@example.com') {
        echo "FAIL: User data mismatch for 'alice'\n";
        return false;
    }
    echo "PASS: Found user 'alice' with correct email\n";
    return true;
}

function testFindUserByEmail() {
    $dbService = new DatabaseService(null);
    $result = $dbService->Query("SELECT * FROM Users WHERE email = 'bob@example.com'");
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find user with email 'bob@example.com'\n";
        return false;
    }
    $user = $result->fetch_assoc();
    if ($user['username'] !== 'bob') {
        echo "FAIL: User data mismatch for email 'bob@example.com'\n";
        return false;
    }
    echo "PASS: Found user with email 'bob@example.com' with correct username\n";
    return true;
}

function testFindStoriesByTitle() {
    $dbService = new DatabaseService(null);
    $result = $dbService->Query("SELECT * FROM Stories WHERE title = 'La forêt enchantée'");
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find story 'La forêt enchantée'\n";
        return false;
    }
    echo "PASS: Found story 'La forêt enchantée'\n";
    return true;
}

function testFindStoriesByAuthor() {
    $dbService = new DatabaseService(null);
    
    // First get Alice's ID
    $userResult = $dbService->Query("SELECT id FROM Users WHERE username = 'alice'");
    if ($userResult === null || $userResult->num_rows == 0) {
        echo "FAIL: Could not find user 'alice'\n";
        return false;
    }
    $aliceData = $userResult->fetch_assoc();
    $aliceId = $aliceData['id'];
    
    // Now find Alice's stories
    $result = $dbService->Query("SELECT * FROM Stories WHERE author_id = '$aliceId'");
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find stories by author 'alice'\n";
        return false;
    }
    echo "PASS: Found stories by author 'alice'\n";
    return true;
}

function testStoryComments() {
    $dbService = new DatabaseService(null);
    
    // Get story ID
    $storyResult = $dbService->Query("SELECT id FROM Stories WHERE title = 'La forêt enchantée'");
    if ($storyResult === null || $storyResult->num_rows == 0) {
        echo "FAIL: Could not find story 'La forêt enchantée'\n";
        return false;
    }
    $storyData = $storyResult->fetch_assoc();
    $storyId = $storyData['id'];
    
    // Test comments on this story
    $result = $dbService->Query("SELECT * FROM StoryComments WHERE story_id = '$storyId'");
    if ($result === null || $result->num_rows < 2) {
        echo "FAIL: Could not find expected comments for 'La forêt enchantée'\n";
        return false;
    }
    echo "PASS: Found comments for 'La forêt enchantée'\n";
    return true;
}

function testParticipations() {
    $dbService = new DatabaseService(null);
    
    $result = $dbService->Query("SELECT p.* FROM Participations p 
                                JOIN StoryParticipations sp ON p.id = sp.participation_id 
                                JOIN Stories s ON s.id = sp.story_id 
                                WHERE s.title = 'La forêt enchantée'");
    
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find participations for 'La forêt enchantée'\n";
        return false;
    }
    
    $participation = $result->fetch_assoc();
    if (strpos($participation['content'], 'La fée') === false) {
        echo "FAIL: Content mismatch for participation\n";
        return false;
    }
    
    echo "PASS: Found participation for 'La forêt enchantée'\n";
    return true;
}

function testParticipationImages() {
    $dbService = new DatabaseService(null);
    
    $result = $dbService->Query("SELECT pi.* FROM participationImage pi 
                                JOIN Participations p ON p.id = pi.participation_id 
                                WHERE p.content LIKE 'La fée%'");
    
    if ($result === null || $result->num_rows == 0) {
        echo "FAIL: Could not find images for participation\n";
        return false;
    }
    
    $image = $result->fetch_assoc();
    if ($image['image_url'] !== 'fairy.png') {
        echo "FAIL: Image URL mismatch for participation\n";
        return false;
    }
    
    echo "PASS: Found correct image for participation\n";
    return true;
}

function testStoryLikes() {
    $dbService = new DatabaseService(null);
    
    // Get story ID
    $storyResult = $dbService->Query("SELECT id FROM Stories WHERE title = 'La forêt enchantée'");
    if ($storyResult === null || $storyResult->num_rows == 0) {
        echo "FAIL: Could not find story 'La forêt enchantée'\n";
        return false;
    }
    $storyData = $storyResult->fetch_assoc();
    $storyId = $storyData['id'];
    
    // Count likes
    $result = $dbService->Query("SELECT COUNT(*) as like_count FROM UserLikes WHERE story_id = '$storyId'");
    if ($result === null) {
        echo "FAIL: Could not count likes for 'La forêt enchantée'\n";
        return false;
    }
    
    $likeData = $result->fetch_assoc();
    if ($likeData['like_count'] != 2) {
        echo "FAIL: Expected 2 likes for 'La forêt enchantée', got {$likeData['like_count']}\n";
        return false;
    }
    
    echo "PASS: Found correct number of likes for 'La forêt enchantée'\n";
    return true;
}

function testStoryFollows() {
    $dbService = new DatabaseService(null);
    
    // Get story ID
    $storyResult = $dbService->Query("SELECT id FROM Stories WHERE title = 'La forêt enchantée'");
    if ($storyResult === null || $storyResult->num_rows == 0) {
        echo "FAIL: Could not find story 'La forêt enchantée'\n";
        return false;
    }
    $storyData = $storyResult->fetch_assoc();
    $storyId = $storyData['id'];
    
    // Count follows
    $result = $dbService->Query("SELECT COUNT(*) as follow_count FROM UserFollows WHERE story_id = '$storyId'");
    if ($result === null) {
        echo "FAIL: Could not count follows for 'La forêt enchantée'\n";
        return false;
    }
    
    $followData = $result->fetch_assoc();
    if ($followData['follow_count'] != 1) {
        echo "FAIL: Expected 1 follow for 'La forêt enchantée', got {$followData['follow_count']}\n";
        return false;
    }
    
    echo "PASS: Found correct number of follows for 'La forêt enchantée'\n";
    return true;
}

function testUserFollowers() {
    $dbService = new DatabaseService(null);
    
    // Get Alice's ID
    $userResult = $dbService->Query("SELECT id FROM Users WHERE username = 'alice'");
    if ($userResult === null || $userResult->num_rows == 0) {
        echo "FAIL: Could not find user 'alice'\n";
        return false;
    }
    $aliceData = $userResult->fetch_assoc();
    $aliceId = $aliceData['id'];
    
    // Count followers
    $result = $dbService->Query("SELECT COUNT(*) as follower_count FROM UserFollowers WHERE followed_id = '$aliceId'");
    if ($result === null) {
        echo "FAIL: Could not count followers for 'alice'\n";
        return false;
    }
    
    $followerData = $result->fetch_assoc();
    if ($followerData['follower_count'] != 2) {
        echo "FAIL: Expected 2 followers for 'alice', got {$followerData['follower_count']}\n";
        return false;
    }
    
    echo "PASS: Found correct number of followers for 'alice'\n";
    return true;
}

function testUserFollows() {
    $dbService = new DatabaseService(null);
    
    // Get Bob's ID
    $userResult = $dbService->Query("SELECT id FROM Users WHERE username = 'bob'");
    if ($userResult === null || $userResult->num_rows == 0) {
        echo "FAIL: Could not find user 'bob'\n";
        return false;
    }
    $bobData = $userResult->fetch_assoc();
    $bobId = $bobData['id'];
    
    // Count people following bob
    $result = $dbService->Query("SELECT COUNT(*) as following_count FROM UserFollowers WHERE followed_id = '$bobId'");
    if ($result === null) {
        echo "FAIL: Could not count people following 'bob'\n";
        return false;
    }
    
    $followData = $result->fetch_assoc();
    if ($followData['following_count'] != 1) {
        echo "FAIL: Expected 1 person following 'bob', got {$followData['following_count']}\n";
        return false;
    }
    
    echo "PASS: Found correct number of followers for 'bob'\n";
    return true;
}

echo "\n=== Running Database Tests ===\n";
testConnection();
testQuery();
testFindUserByUsername();
testFindUserByEmail();
testFindStoriesByTitle();
testFindStoriesByAuthor();
testStoryComments();
testParticipations();
testParticipationImages();
testStoryLikes();
testStoryFollows();
testUserFollowers();
testUserFollows();
echo "=== Tests Complete ===\n";