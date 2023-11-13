import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from '@angular/material/dialog';
import {MailingService} from '../../../../services/mailing.service';

@Component({
    selector: 'km-send-test-mailing',
    templateUrl: './send-test-mailing.component.html',
    styleUrls: ['./send-test-mailing.component.sass']
})
export class SendTestMailingComponent implements OnInit {

    public mailing: any = {};
    public name: string;
    public emailAddress: string;
    public title: string;
    public fromAddress: string;
    public fromName: string;
    public replyToAddress: string;
    public replyToName: string;


    constructor(public dialogRef: MatDialogRef<SendTestMailingComponent>,
                @Inject(MAT_DIALOG_DATA) public data: any,
                private mailingService: MailingService) {
    }

    ngOnInit(): void {
        this.mailing = this.data.mailing;
    }

    public async sendTest() {
        let fromAddress = null;
        let replyAddress = null;
        if (this.fromAddress) {
            fromAddress = this.fromName ? `${this.fromName}<${this.fromAddress}>` : this.fromAddress;
        }
        if (this.replyToAddress) {
            replyAddress = this.replyToName ? `${this.replyToName}<${this.replyToAddress}>` : this.replyToAddress;
        }

        await this.mailingService.sendMailingTest(this.name, this.emailAddress, this.mailing, fromAddress, replyAddress);
    }

}
