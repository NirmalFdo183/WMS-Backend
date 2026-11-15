// Deploy pipeline for the VPS (deploys the dev branch). Jenkins runs in Docker on the same server with the
// Docker socket and /opt/wms mounted, so it can update and restart the app stack.
pipeline {
    agent any

    options {
        disableConcurrentBuilds()
        timeout(time: 20, unit: 'MINUTES')
    }

    stages {
        stage('Pull latest code') {
            steps {
                sh 'git config --global --add safe.directory /opt/wms'
                sh 'git -C /opt/wms pull --ff-only origin dev'
            }
        }

        stage('Build image') {
            steps {
                sh 'cd /opt/wms && docker compose build app'
            }
        }

        stage('Deploy') {
            steps {
                sh 'cd /opt/wms && docker compose up -d'
            }
        }

        stage('Health check') {
            steps {
                sh '''
                    cd /opt/wms
                    for i in $(seq 1 24); do
                        if docker compose exec -T app curl -fsS http://127.0.0.1:8000/api/ping; then
                            echo "App is healthy"
                            exit 0
                        fi
                        sleep 5
                    done
                    echo "App did not become healthy in 2 minutes"
                    docker compose logs --tail 50 app
                    exit 1
                '''
            }
        }
    }

    post {
        always {
            // Remove old dangling images so the 20 GB disk doesn't fill up
            sh 'docker image prune -f || true'
        }
    }
}
