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

        const fromAddress = this.mailing.mailingProfile ? this.mailing.mailingProfile.fromAddress : '';
        if (fromAddress.includes('<')) {
            this.fromName = fromAddress.split('<')[0].trim();
            this.fromAddress = fromAddress.split('<')[1].replace('>', '').trim();
        } else {
            this.fromAddress = fromAddress;
        }

        const replyAddress = this.mailing.mailingProfile ? this.mailing.mailingProfile.replyToAddress : '';
        if (replyAddress.includes('<')) {
            this.replyToName = replyAddress.split('<')[0].trim();
            this.replyToAddress = replyAddress.split('<')[1].replace('>', '').trim();
        } else {
            this.replyToAddress = replyAddress;
        }
    }

    public async sendTest() {
        let fromAddress = null;
        let replyAddress = null;
        if (this.fromAddress) {
            fromAddress = this.fromName ? `${this.fromName} <${this.fromAddress}>` : this.fromAddress;
        }
        if (this.replyToAddress) {
            replyAddress = this.replyToName ? `${this.replyToName} <${this.replyToAddress}>` : this.replyToAddress;
        }

        await this.mailingService.sendMailingTest(this.name, this.emailAddress, this.mailing, fromAddress, replyAddress);
        this.dialogRef.close();
    }

    public close() {
        this.dialogRef.close();
    }

}
