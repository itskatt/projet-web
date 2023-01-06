import { ErrorHandler, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { ReactiveFormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ArticlesGridComponent } from './articles-grid/articles-grid.component';
import { RegisterComponent } from './register/register.component';
import { LoginComponent } from './login/login.component';
import { ArticleComponent } from './article/article.component';
import { CartComponent } from './cart/cart.component';
import { HttpInterceptorService } from './service/http-interceptor.service';
import { ErrorHandlerService } from './service/error-handler.service';

@NgModule({
    declarations: [
        AppComponent,
        ArticlesGridComponent,
        RegisterComponent,
        LoginComponent,
        ArticleComponent,
        CartComponent,
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
            useClass: ErrorHandlerService
        }
    ],
    bootstrap: [AppComponent],
})
export class AppModule {}
