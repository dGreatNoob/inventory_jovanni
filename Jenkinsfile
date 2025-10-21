pipeline {
    agent any

    environment {
        // GitHub repository information
        GITHUB_REPO = 'john123911/spc'
        GITHUB_BRANCH = 'Jess-main'
        
        // Docker environment
        DOCKER_COMPOSE_DIR = "${WORKSPACE}"

        APP_URL = 'http://localhost:3001'
    }
    
    stages {
        stage('Checkout') {
            steps {
                // Clean workspace before checkout
                cleanWs()
                
                // Clone the repository
                git branch: "${GITHUB_BRANCH}", 
                    url: "https://github.com/${GITHUB_REPO}.git",
                    credentialsId: 'spc-github' // Create this credential in Jenkins
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                cd ${DOCKER_COMPOSE_DIR}
                docker-compose -p spc build app
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                cd ${DOCKER_COMPOSE_DIR}
                docker-compose -p spc run --rm app php artisan test
                '''
            }
        }

        stage('QA Test Environment') {
            steps {
                script {
                    echo 'Testing environment...'
                }
            }
        }
        
        stage('Build Environment') {
            steps {
                // Navigate to deployment directory and copy files (if Jenkins workspace is different from deployment)
                sh '''
                # Ensure deployment directory exists
                mkdir -p ${DOCKER_COMPOSE_DIR}

                # Copy all files to deployment directory
                rsync -av --exclude='.git' --exclude='node_modules' --exclude='vendor' ./ ${DOCKER_COMPOSE_DIR}/

                # Move to deployment directory
                cd ${DOCKER_COMPOSE_DIR}

                # Check if containers are up
                if docker-compose -p spc ps -q | grep -q .; then
                    echo "Containers are running. Shutting them down..."
                    docker-compose -p spc down
                else
                    echo "Containers are already down. Skipping shutdown."
                fi
                
                #---UNDER CONSTRUCTION---
                #docker-compose -p spc build app
                #docker-compose -p spc up -d
                # Wait for services to be ready
                #sleep 10
                # Run migrations
                #docker-compose exec -T app php artisan migrate --no-interaction --force
                '''
            }
        }

        stage('Feature Manual Test Approval') {
            steps {
                script {
                    echo 'Manual Testing environment...'
                }
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploy environment...'
            }
        }
    }
    
    post {
        success {
            echo 'Deployment successful!'
        }
        failure {
            echo 'Deployment failed!'
        }
    }
}