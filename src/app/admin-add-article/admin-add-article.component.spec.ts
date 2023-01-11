import { ComponentFixture, TestBed } from "@angular/core/testing";

import { AdminAddArticleComponent } from "./admin-add-article.component";

describe("AdminAddArticleComponent", () => {
    let component: AdminAddArticleComponent;
    let fixture: ComponentFixture<AdminAddArticleComponent>;

    beforeEach(async () => {
        await TestBed.configureTestingModule({
            declarations: [AdminAddArticleComponent],
        }).compileComponents();

        fixture = TestBed.createComponent(AdminAddArticleComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it("should create", () => {
        expect(component).toBeTruthy();
    });
});
