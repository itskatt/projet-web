import { Component } from '@angular/core';
import { CurrentUserService } from './service/current-user.service';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.css']
})
export class AppComponent {
    title = 'projet-web';

    constructor(public currentUser: CurrentUserService) { }

    logout(): void {
        this.currentUser.localLogout()
        // todo : http logout
    }
}
