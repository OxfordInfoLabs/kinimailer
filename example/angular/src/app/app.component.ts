import {Component, OnInit} from '@angular/core';
import {Subscription} from 'rxjs';
import {ActivationEnd, Router} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {MailingProfilesComponent} from 'ngx-kinimailer';

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.sass']
})
export class AppComponent implements OnInit{

    public title = '';

    private routerSub: Subscription;

    constructor(private router: Router,
                private dialog: MatDialog) {

        this.routerSub = this.router.events.subscribe((e: any) => {
            const main = document.getElementById('main');
            if (main) {
                main.scrollTo(0, 0);
            }

            if (e instanceof ActivationEnd) {
                const data: any = e.snapshot.data
                this.title = data.title;
                if (e.snapshot.firstChild && e.snapshot.firstChild.data) {
                    const data: any = e.snapshot.firstChild.data;
                    this.title = data.title;
                }
            }
        });
    }

    ngOnInit() {
    }

    public mailingProfiles() {
        const dialogRef = this.dialog.open(MailingProfilesComponent, {
            width: '1200px',
            height: '800px'
        });

        dialogRef.afterClosed().subscribe(res => {

        });
    }

}
