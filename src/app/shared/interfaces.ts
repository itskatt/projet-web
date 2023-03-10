type Status = "success" | "error";

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

export interface SingleArticle extends Article {
    price_no_tax: string;
}

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
    article: SingleArticle;
}

export interface WarningElement {
    article_id: number;
    quantity: number;
}

export interface LoginResponse extends Statusable {
    last_name: string;
    first_name: string;
    admin: boolean;
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
    invoice_id: number;
    num_articles: number | string;
}

export interface CurrentCartResponse extends Statusable, Priceable {
    cart_id: number;
    articles: CartArticle[];
}

export interface CartUpdateStatement {
    article_id: number;
    quantity: number;
}

interface InvoiceSales {
    id: number;
    sales: number;
}

interface TopArticle {
    article_id: number;
    total_quantity: number;
    turnover: number;
}

interface HighStock {
    name: string;
    article_id: number;
    quantity: number;
}

interface SupplierStockInfo {
    supplier_name: string;
    num_articles: number;
}

export interface AdminStatsResponse extends Statusable {
    sales_per_invoice: InvoiceSales[];
    turnover: number;
    most_sold_articles: TopArticle[];
    highest_stocks: HighStock[];
    num_articles_per_supplier: SupplierStockInfo[];
}

export interface ArticleStockUpdateStatement {
    article_id: number;
    quantity: number;
}

export interface CreateArticleResponse extends Statusable {
    article_id: number;
}
