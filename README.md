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

# 3. Créer le secret des identifiants BDD, puis déployer la base de données
kubectl create secret generic postgres-credentials \
  --from-literal=POSTGRES_DB=<db_name> \
  --from-literal=POSTGRES_USER=<db_user> \
  --from-literal=POSTGRES_PASSWORD=<db_password>
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

## ☁️ Déploiement Terraform (GKE)

Le cluster Kubernetes de production est provisionné via Terraform sur Google Kubernetes Engine.

### Infrastructure
- Cluster GKE **zonal** (`europe-west1-b`), couvert par le free tier GKE ($74.40 de crédits mensuels)
- Node pool : 1 node `e2-small` (2 vCPU partagés, 2 Go RAM)
- Fichiers : `terraform/main.tf`, `terraform/variables.tf`

### Provisionner le cluster

```bash
cd terraform

# Configurer votre project ID GCP
cp terraform.tfvars.example terraform.tfvars
# éditer terraform.tfvars avec votre project_id

# Authentification
gcloud auth application-default login

# Provisionner
terraform init
terraform plan
terraform apply
```

### Se connecter au cluster

```bash
gcloud container clusters get-credentials shop-menace-kult-cluster --zone europe-west1-b
kubectl get nodes
```

### Déployer l'application sur GKE

Les images étant privées sur GHCR, un secret de pull est nécessaire :

```bash
# Secret pour tirer les images depuis GHCR
kubectl create secret docker-registry ghcr-secret \
  --docker-server=ghcr.io \
  --docker-username=<votre-username-github> \
  --docker-password=<votre-token-github-avec-read:packages> \
  --docker-email=<votre-email>

# Secret pour la base de données
kubectl create secret generic postgres-credentials \
  --from-literal=POSTGRES_DB=<db_name> \
  --from-literal=POSTGRES_USER=<db_user> \
  --from-literal=POSTGRES_PASSWORD=<db_password>

# Déploiement
kubectl apply -f k8s/database/postgres.yaml
helm install shop-menace-kult ./k8s/shop-menace-kult

# Migrations
kubectl exec deployment/shop-menace-kult-php-fpm -- php bin/console doctrine:migrations:migrate --no-interaction

# Récupérer l'IP publique (Service LoadBalancer)
kubectl get svc shop-menace-kult-nginx
```

### ⚠️ Coûts et nettoyage

Le cluster GKE et le LoadBalancer consomment des ressources facturées en continu. Pour éviter toute facturation inutile :

```bash
helm uninstall shop-menace-kult
kubectl delete -f k8s/database/postgres.yaml
cd terraform
terraform destroy
```
