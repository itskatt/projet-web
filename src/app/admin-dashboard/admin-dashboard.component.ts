import { Component, OnInit } from '@angular/core';
import { HttpClientService } from '../service/http-client.service';

@Component({
    selector: 'app-admin-dashboard',
    templateUrl: './admin-dashboard.component.html',
    styleUrls: ['./admin-dashboard.component.css']
})
export class AdminDashboardComponent implements OnInit {

    constructor(private client: HttpClientService) {}

    ngOnInit(): void {
        this.client.getAdminStats().subscribe(stats => {
            console.log(stats);
        })
    }

}
