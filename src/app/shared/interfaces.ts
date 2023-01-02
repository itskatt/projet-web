type Status = "success" | "error";

export interface Statusable {
    status: Status
}

export interface Article {
    article_id: number,
    article_name: string,
    description: string,
    rating: number,
    year: number,
    price_tax: string | number,
    image: string,
    supplier_name: string,
    quantity: number
}

export interface ArticlesResponse extends Statusable {
    articles: Article[]
}
