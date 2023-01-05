import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { HttpClientService } from '../service/http-client.service';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css', "../shared/forms.css"]
})
export class LoginComponent {
    loginForm: FormGroup;

    constructor(private client: HttpClientService) {
        this.loginForm = new FormGroup({
            email: new FormControl(""),
            password: new FormControl(""),
            remember: new FormControl(false)
        });
    }

    submit(): void {
        if (!this.loginForm.valid) {
            return;
        }

        this.client.login(
            this.loginForm.value.email,
            this.loginForm.value.password,
            this.loginForm.value.remember
        ).subscribe(response => {
            console.log(response) // TODO stocker les infos pour les concerver de page en page
        })
    }
}
