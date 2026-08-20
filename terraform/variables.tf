variable "project_id" {
  description = "ID du projet GCP"
  type        = string
}

variable "region" {
  description = "Région GCP"
  type        = string
  default     = "europe-west1"
}

variable "zone" {
  description = "Zone GCP (pour un cluster zonal, couvert par le free tier)"
  type        = string
  default     = "europe-west1-b"
}
