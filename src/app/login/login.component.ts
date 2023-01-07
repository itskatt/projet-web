import { HttpErrorResponse } from '@angular/common/http';
import { Component } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { CurrentUserService } from '../service/current-user.service';
import { HttpClientService } from '../service/http-client.service';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css', '../shared/forms.css'],
})
export class LoginComponent {
    loginForm: FormGroup;
    loginError: string = "";

    constructor(private client: HttpClientService, private router: Router, private user: CurrentUserService) {
        this.loginForm = new FormGroup({
            email: new FormControl(''),
            password: new FormControl(''),
            remember: new FormControl(false),
        });
    }

    submit(): void {
        if (!this.loginForm.valid) {
            return;
        }

        this.client
            .login(
                this.loginForm.value.email,
                this.loginForm.value.password,
                this.loginForm.value.remember
            )
            .subscribe({
                next: (response) => {
                    this.user.localLogin(this.loginForm.value.email, response.last_name, response.first_name);

                    this.router.navigate(['']);
                },
                error: (error: Error) => {
                    if (error instanceof HttpErrorResponse) {
                        if (error.status === 401) {
                            this.loginError = error.error.message;
                            return
                        }
                    }

                    throw error;
                }
            });
    }
}
