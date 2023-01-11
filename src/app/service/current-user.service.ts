import { Injectable } from "@angular/core";

/**
 * Service d'utilité pour l'utilisateur actuelement connecté.
 */
@Injectable({
    providedIn: "root",
})
export class CurrentUserService {
    constructor() {}

    /**
     * Renvoie True si un utilisateur est actuelement connecté.
     */
    isLoggedIn(): boolean {
        return this.email != null;
    }

    /**
     * Déconecte l'utilisateur localement, effacant les données dans le local storage.
     */
    localLogout(): void {
        localStorage.removeItem("email");
        localStorage.removeItem("last_name");
        localStorage.removeItem("first_name");
        localStorage.removeItem("admin");
    }

    /**
     * Enregistre les données de l'utilisateur en local.
     */
    localLogin(email: string, last_name: string, first_name: string, admin: boolean): void {
        localStorage.setItem("email", email);
        localStorage.setItem("last_name", last_name);
        localStorage.setItem("first_name", first_name);
        localStorage.setItem("admin", "" + admin);
    }

    get email(): string | null {
        return localStorage.getItem("email");
    }

    get lastName(): string | null {
        return localStorage.getItem("last_name");
    }

    get firstName(): string | null {
        return localStorage.getItem("first_name");
    }

    get admin(): string | null {
        return localStorage.getItem("admin");
    }
}
