pipeline {
    agent any

    environment {
        DEPLOY_PATH = "/var/www/laravel-app"
    }

    stages {

        stage('Pull Code') {
            steps {
                echo 'Pulling latest code from GitHub...'
                sh '''
                cd $DEPLOY_PATH
                git pull origin main
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                cd $DEPLOY_PATH
                composer install --no-interaction --prefer-dist --optimize-autoloader
                '''
            }
        }

        stage('Migrate Database') {
            steps {
                sh '''
                cd $DEPLOY_PATH
                php artisan migrate --force
                '''
            }
        }

        stage('Optimize Laravel') {
            steps {
                sh '''
                cd $DEPLOY_PATH
                php artisan config:cache
                php artisan route:cache
                php artisan view:cache
                '''
            }
        }

        stage('Restart Services') {
            steps {
                sh '''
                sudo systemctl restart php8.3-fpm
                sudo systemctl restart nginx
                '''
            }
        }
    }

    post {
        success {
            echo '🚀 Deployment Successful!'
        }
        failure {
            echo '❌ Deployment Failed!'
        }
    }
}