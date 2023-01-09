import { Component } from "@angular/core";
import { CurrentUserService } from "./service/current-user.service";
import { HttpClientService } from "./service/http-client.service";

@Component({
    selector: "app-root",
    templateUrl: "./app.component.html",
    styleUrls: ["./app.component.css"],
})
export class AppComponent {
    title = "projet-web";

    constructor(
        public currentUser: CurrentUserService,
        private client: HttpClientService
    ) {}

    logout(): void {
        this.client.logout().subscribe();
        this.currentUser.localLogout();
    }
}
