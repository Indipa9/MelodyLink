<?php
class Merchandise extends Controller {
    private $merchandiseModel;

    public function __construct() {
        $this->merchandiseModel = $this->model('m_Merchandise');
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function index() {
        $merchandise = $this->merchandiseModel->getAllMerchandise();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $data = [
            'merchandise' => $merchandise,
            'isLoggedIn' => $this->isAuthenticated(),
            'cartItems' => $_SESSION['cart'] ?? [],
            'user_id' => $_SESSION['user_id'] ?? null
        ];
    
        $this->view('v_merchandise', $data);
    }

    public function addToCart() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            if (isset($data->merchId)) {
                $merchId = $data->merchId;
                
                // Get merchandise details
                $merchandise = $this->merchandiseModel->getMerchandiseById($merchId);
                
                if ($merchandise) {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    
                    // Check if item already exists in cart
                    if (isset($_SESSION['cart'][$merchId])) {
                        $_SESSION['cart'][$merchId]['quantity']++;
                    } else {
                        $_SESSION['cart'][$merchId] = [
                            'id' => $merchId,
                            'name' => $merchandise->Name,
                            'price' => $merchandise->Price,
                            'quantity' => 1,
                            'image' => $merchandise->image
                        ];
                    }
                    
                    $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Item added to cart successfully',
                        'cartCount' => $totalItems
                    ]);
                    return;
                }
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add item to cart'
        ]);
    }

    public function getCartCount() {
        header('Content-Type: application/json');
        $totalItems = 0;
        
        if (isset($_SESSION['cart'])) {
            $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
        }
        
        echo json_encode(['cartCount' => $totalItems]);
    }

    public function removeFromCart() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            
            if (isset($data->merchId) && isset($_SESSION['cart'][$data->merchId])) {
                unset($_SESSION['cart'][$data->merchId]);
                
                $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cartCount' => $totalItems
                ]);
                return;
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove item from cart'
        ]);
    }
}