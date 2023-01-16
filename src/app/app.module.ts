import { ErrorHandler, NgModule } from "@angular/core";
import { BrowserModule } from "@angular/platform-browser";
import { HttpClientModule, HTTP_INTERCEPTORS } from "@angular/common/http";
import { ReactiveFormsModule } from "@angular/forms";

import { AppRoutingModule } from "./app-routing.module";
import { AppComponent } from "./app.component";
import { ArticlesGridComponent } from "./articles-grid/articles-grid.component";
import { RegisterComponent } from "./register/register.component";
import { LoginComponent } from "./login/login.component";
import { ArticleComponent } from "./article/article.component";
import { CartComponent } from "./cart/cart.component";
import { HttpInterceptorService } from "./service/http-interceptor.service";
import { ErrorHandlerService } from "./service/error-handler.service";
import { PreviousCartComponent } from "./previous-cart/previous-cart.component";
import { AdminDashboardComponent } from "./admin-dashboard/admin-dashboard.component";
import { AdminAddArticleComponent } from "./admin-add-article/admin-add-article.component";
import { PageNotFoundComponent } from "./page-not-found/page-not-found.component";
import { StarRatingComponent } from "./star-rating/star-rating.component";

@NgModule({
    declarations: [
        AppComponent,
        ArticlesGridComponent,
        RegisterComponent,
        LoginComponent,
        ArticleComponent,
        CartComponent,
        PreviousCartComponent,
        AdminDashboardComponent,
        AdminAddArticleComponent,
        PageNotFoundComponent,
        StarRatingComponent,
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        HttpClientModule,
        ReactiveFormsModule,
    ],
    providers: [
        {
            provide: HTTP_INTERCEPTORS,
            useClass: HttpInterceptorService,
            multi: true,
        },
        {
            provide: ErrorHandler,
            useClass: ErrorHandlerService,
        },
    ],
    bootstrap: [AppComponent],
})
export class AppModule {}
