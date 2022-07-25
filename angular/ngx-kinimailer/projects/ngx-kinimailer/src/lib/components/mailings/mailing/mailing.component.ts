import {Component, OnInit} from '@angular/core';
import {MailingService} from '../../../services/mailing.service';
import {ActivatedRoute} from '@angular/router';
import {MatDialog} from '@angular/material/dialog';
import {NewMailingComponent} from './new-mailing/new-mailing.component';

@Component({
    selector: 'km-mailing',
    templateUrl: './mailing.component.html',
    styleUrls: ['./mailing.component.sass']
})
export class MailingComponent implements OnInit {

    public mailing: any = {};

    private mailingId: number;

    constructor(private mailingService: MailingService,
                private route: ActivatedRoute,
                private dialog: MatDialog) {
    }

    ngOnInit(): void {
        this.route.params.subscribe(params => {
            this.mailingId = params.id;
            this.loadMailing();
        });
    }

    public openNewMailingDialog() {
        const dialogRef = this.dialog.open(NewMailingComponent, {
            width: '1200px',
            height: '800px',
        });

        dialogRef.afterClosed().subscribe(res => {

        });
    }

    private async loadMailing(): Promise<void> {
        this.mailing = await this.mailingService.getMailing(this.mailingId);
        this.mailingId = this.mailing.id;
        if (!this.mailingId) {
            this.openNewMailingDialog();
        }
    }



}
