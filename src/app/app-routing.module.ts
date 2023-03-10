import { NgModule } from "@angular/core";
import { RouterModule, Routes } from "@angular/router";
import { ArticleComponent } from "./article/article.component";
import { ArticlesGridComponent } from "./articles-grid/articles-grid.component";
import { CartComponent } from "./cart/cart.component";
import { PreviousCartComponent } from "./previous-cart/previous-cart.component";
import { LoginComponent } from "./login/login.component";
import { RegisterComponent } from "./register/register.component";
import { AdminDashboardComponent } from "./admin-dashboard/admin-dashboard.component";
import { AdminAddArticleComponent } from "./admin-add-article/admin-add-article.component";
import { PageNotFoundComponent } from "./page-not-found/page-not-found.component";

const routes: Routes = [
    {
        path: "",
        component: ArticlesGridComponent,
    },
    {
        path: "article/:id",
        component: ArticleComponent,
    },
    {
        path: "register",
        component: RegisterComponent,
    },
    {
        path: "login",
        component: LoginComponent,
    },
    {
        path: "cart",
        component: CartComponent,
    },
    {
        path: "previous-cart/:id",
        component: PreviousCartComponent,
    },
    {
        path: "admin",
        component: AdminDashboardComponent,
    },
    {
        path: "new-article",
        component: AdminAddArticleComponent,
    },
    {
        path: "**",
        pathMatch: "full",
        component: PageNotFoundComponent,
    },
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
})
export class AppRoutingModule {}
