<?php
// delete_account.php


require_once 'dashboard.php'; // Adjust path as needed

function deleteUserAccount($userId, $password) {
    try {
        $pdo = connectDatabase();
        
        // First verify the password
        $stmt = $pdo->prepare("SELECT password_hash FROM member WHERE member_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }
        
        // Start transaction
        $pdo->beginTransaction();
        
        try {
            // Delete user's activities
            $stmt = $pdo->prepare("DELETE FROM user_activities WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Delete user's playlists
            $stmt = $pdo->prepare("DELETE FROM playlists WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Delete user's profile picture (if exists)
            $stmt = $pdo->prepare("SELECT profile_pic FROM member WHERE member_id = ?");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch();
            
            if ($profile && $profile['profile_pic']) {
                $picPath = './uploads/' . $profile['profile_pic'];
                if (file_exists($picPath)) {
                    unlink($picPath);
                }
            }
            
            // Finally, delete the user account
            $stmt = $pdo->prepare("DELETE FROM member WHERE member_id = ?");
            $stmt->execute([$userId]);
            
            // Commit transaction
            $pdo->commit();
            
            // Clear session
            session_destroy();
            
            return ['success' => true, 'message' => 'Account deleted successfully'];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_account') {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    $password = $_POST['password'] ?? '';
    
    if (empty($password)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit;
    }
    
    $result = deleteUserAccount($_SESSION['user_id'], $password);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>