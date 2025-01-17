#include <stdio.h>
#include <stdlib.h>
#include <math.h>

typedef struct Node {
    int data;
    struct Node* leftChild;
    struct Node* rightChild;
} Node;

Node* createNode(int data) {
    Node* newNode = (Node*)malloc(sizeof(Node));
    newNode->data = data;
    newNode->leftChild = NULL;
    newNode->rightChild = NULL;
    return newNode;
}

Node* insertNode(Node* root, int data) {
    if (root == NULL) {
        return createNode(data);
    }
    if (data < root->data) {
        root->leftChild = insertNode(root->leftChild, data);
    } else {
        root->rightChild = insertNode(root->rightChild, data);
    }
    return root;
}

void inOrderTraversal(Node* root) {
    if (root != NULL) {
        inOrderTraversal(root->leftChild);
        printf("%d ", root->data);
        inOrderTraversal(root->rightChild);
    }
}

Node* rightRotate(Node* node) {
    Node* left = node->leftChild;
    node->leftChild = left->rightChild;
    left->rightChild = node;
    return left;
}

Node* createVine(Node* root) {
    Node* dummyNode = createNode(0);
    dummyNode->rightChild = root;
    Node* vine = dummyNode;

    while (vine->rightChild != NULL) {
        if (vine->rightChild->leftChild == NULL) {
            vine = vine->rightChild;
        } else {
            vine->rightChild = rightRotate(vine->rightChild);
        }
    }
    Node* newRoot = dummyNode->rightChild;
    free(dummyNode);
    return newRoot;
}

Node* leftRotate(Node* node) {
    Node* right = node->rightChild;
    node->rightChild = right->leftChild;
    right->leftChild = node;
    return right;
}

Node* compress(Node* vineRoot, int count) {
    Node* temp = vineRoot;
    for (int i = 0; i < count; i++) {
        if (temp->rightChild != NULL) {
            temp->rightChild = leftRotate(temp->rightChild);
            temp = temp->rightChild;
        }
    }
    return vineRoot;
}

Node* createBalancedTree(Node* vineRoot, int numOfNodes) {
    int m = pow(2, floor(log2(numOfNodes + 1))) - 1;
    vineRoot = compress(vineRoot, numOfNodes - m);
    while (m > 1) {
        m /= 2;
        vineRoot = compress(vineRoot, m);
    }
    return vineRoot;
}
int countNodes(Node* root) {
    if (root == NULL) return 0;
    return 1 + countNodes(root->leftChild) + countNodes(root->rightChild);
}

int main() {
    Node* root = NULL;

    // Create an unbalanced tree
    root = insertNode(root, 10);
    root = insertNode(root, 5);
    root = insertNode(root, 15);
    root = insertNode(root, 3);
    root = insertNode(root, 7);

    printf("Original Tree (In-order): ");
    inOrderTraversal(root);
    printf("\n");

    // Step 1: Convert to vine
    root = createVine(root);

    // Step 2: Balance the tree
    int numOfNodes = countNodes(root);
    root = createBalancedTree(root, numOfNodes);

    printf("Balanced Tree (In-order): ");
    inOrderTraversal(root);
    printf("\n");

    return 0;
}