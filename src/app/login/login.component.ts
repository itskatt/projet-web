import { HttpErrorResponse } from '@angular/common/http';
import { Component } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { HttpClientService } from '../service/http-client.service';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css', '../shared/forms.css'],
})
export class LoginComponent {
    loginForm: FormGroup;

    constructor(private client: HttpClientService, private router: Router) {
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
                    localStorage.setItem('email', this.loginForm.value.email);
                    localStorage.setItem('last_name', response.last_name);
                    localStorage.setItem('first_name', response.first_name);
    
                    this.router.navigate(['']);
                },
                error: (error: Error) => {
                    if (error instanceof HttpErrorResponse) {
                        if (error.status === 401) {
                            console.log("mdp incorect")
                            return
                        }
                    }

                    throw error;
                    
                }
            });
    }
}
