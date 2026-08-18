# Shop the menace kult

> Online store selling resin figures and figures painted in the "grimdark" style.

## 🐳 Stack
- PHP 8.4
- Symfony 7.4.*
- Composer 2.x
- Docker Compose

## 🚀 Déploiement Kubernetes

Ce projet peut être déployé sur Kubernetes via un chart Helm.

### Stack de déploiement
- **Helm chart** (`k8s/shop-menace-kult/`) : deux Deployments distincts (`php-fpm` et `nginx`), chacun scalable indépendamment
- **PostgreSQL** (`k8s/database/`) : déployé séparément du chart applicatif (bonne pratique pour une base stateful), avec PersistentVolumeClaim pour la persistance des données
- **Ingress** : exposition HTTP/HTTPS via ingress-nginx

### Déployer en local (minikube)

```bash
# 1. Démarrer minikube et pointer Docker vers son environnement interne
minikube start
eval $(minikube docker-env)

# 2. Builder les images applicatives
docker build -f docker/Dockerfile --target php_fpm --build-arg APP_ENV=prod -t shop-menace-kult-php-fpm:local .
docker build -f docker/Dockerfile --target nginx --build-arg APP_ENV=prod -t shop-menace-kult-nginx:local .

# 3. Déployer la base de données
kubectl apply -f k8s/database/postgres.yaml

# 4. Déployer l'application via Helm
helm install shop-menace-kult ./k8s/shop-menace-kult

# 5. Lancer les migrations
kubectl exec deployment/shop-menace-kult-php-fpm -- php bin/console doctrine:migrations:migrate --no-interaction

# 6. Accéder à l'application (port-forward)
kubectl port-forward service/shop-menace-kult-nginx 8443:443
# puis ajouter "127.0.0.1 prod.shop-menace-kult.fr" à /etc/hosts
# et ouvrir https://prod.shop-menace-kult.fr:8443/
```

### Notes
- En environnement WSL2, `minikube tunnel` ne rend pas l'Ingress routable depuis Windows — utiliser `kubectl port-forward` à la place.
- Les images sont buildées en local pour les tests (`pullPolicy: Never` dans `values.yaml`) ; un pipeline CI/CD est prévu pour build/push automatique vers un registre.
