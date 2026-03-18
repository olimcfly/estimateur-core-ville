from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    app_name: str = "Estimateur Immobilier Intelligent"
    environment: str = "development"
    database_url: str = "sqlite:///./immobilier.db"
    openai_api_key: str = ""
    perplexity_api_key: str = ""
    admin_token: str = "change-me"

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8")


settings = Settings()
