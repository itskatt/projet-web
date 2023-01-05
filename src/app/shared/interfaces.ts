type Status = 'success' | 'error';

export interface Statusable {
    status: Status;
}

export interface Article {
    article_id: number;
    article_name: string;
    description: string;
    rating: number;
    year: number;
    price_tax: string | number;
    image: string;
    supplier_name: string;
    quantity: number;
}

export interface ArticlesResponse extends Statusable {
    articles: Article[];
}

export interface ArticleResponse extends Statusable {
    article: Article;
}

export interface WarningElement {
    article_id: number;
    quantity: number;
}

export interface LoginResponse extends Statusable {
    last_name: string;
    first_name: string;
    warnings: WarningElement[];
}

export interface Invoice {
    invoice_id: number;
    cart_id: number;
    created: string | Date;
    num_articles: string | number | null;
}

export interface CartInvoicesResponse extends Statusable {
    invoices: Invoice[];
}
