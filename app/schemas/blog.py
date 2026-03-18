from pydantic import BaseModel


class ArticleGenerationInput(BaseModel):
    city: str
    topic_hint: str


class ArticleCreate(BaseModel):
    title: str
    intro: str
    body_markdown: str
    conclusion: str
    faq: str
    is_published: bool = True
