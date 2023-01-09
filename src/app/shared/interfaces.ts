type Status = 'success' | 'error';

export interface Statusable {
    status: Status;
}

interface Priceable {
    price_no_tax: number;
    price_tax: number;
}

interface BaseArticle {
    article_id: number;
    article_name: string;
    description: string;
    rating: number;
    year: number;
    price_tax: string | number;
    image: string;
    supplier_name: string;
}

export interface Article extends BaseArticle {
    quantity: number;
}

// TODO : ce truc n'est pas complet, on peut le changer en fonction des besoins
export interface SoldArticle extends BaseArticle {
    cart_quantity: number;
}

export interface CartArticle extends SoldArticle {
    stock_quantity: number;
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
    created: string;
    num_articles: string | number | null;
}

export interface CartInvoicesResponse extends Statusable {
    invoices: Invoice[];
}

export interface PreviousInvoiceResponse extends Statusable, Priceable {
    articles: SoldArticle[];
}

export interface CurrentCartResponse extends Statusable, Priceable {
    cart_id: number;
    articles: CartArticle[];
}

export interface CartUpdateStatement {
    article_id: number,
    quantity: number
}
