import { HttpErrorResponse } from "@angular/common/http";
import { ErrorHandler, Injectable } from "@angular/core";

@Injectable({
    providedIn: "root",
})
export class ErrorHandlerService implements ErrorHandler {
    constructor() {}

    handleError(error: Error): void {
        if (error instanceof HttpErrorResponse) {
            console.group("Http error in global EH");
            console.error(error.status);
            console.error(error.error.message);
            console.groupEnd();

            return;
        }

        console.group("Uncaugh error");
        console.error(error);
        console.groupEnd();
    }
}
