pipeline {
    agent any

    environment {
        APP_DIR = '/var/www/laravel-app'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main',
                    url: 'https://github.com/haiider820/jenkins-ec2.git'
            }
        }

        stage('Prepare App Directory') {
            steps {
                sh '''
                    sudo mkdir -p $APP_DIR
                    sudo chown -R jenkins:jenkins $APP_DIR
                '''
            }
        }

        stage('Copy Project Files') {
            steps {
                sh '''
                    rsync -av --delete \
                    --exclude='.git' \
                    --exclude='.env' \
                    --exclude='vendor' \
                    --exclude='node_modules' \
                    ./ $APP_DIR/
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                    cd $APP_DIR
                    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
                '''
            }
        }

        stage('Build Frontend Assets') {
            steps {
                sh '''
                    cd $APP_DIR
                    if [ -f package.json ]; then
                        npm install
                        npm run build
                    fi
                '''
            }
        }

        stage('Laravel Commands') {
            steps {
                sh '''
                    cd $APP_DIR

                    php artisan migrate --force
                    php artisan config:cache
                    php artisan route:cache
                    php artisan view:cache

                    sudo chown -R www-data:www-data storage bootstrap/cache
                    sudo chmod -R 775 storage bootstrap/cache
                '''
            }
        }

        stage('Restart Services') {
            steps {
                sh '''
                    sudo systemctl restart php8.3-fpm || sudo systemctl restart php8.2-fpm || true
                    sudo systemctl restart nginx
                '''
            }
        }
    }

    post {
        success {
            echo 'Laravel deployed successfully'
        }
        failure {
            echo 'Deployment failed'
        }
    }
}
