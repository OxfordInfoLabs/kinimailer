import {Component, OnInit} from '@angular/core';
import {ActivatedRoute} from '@angular/router';
import {MailingService} from '../../../../services/mailing.service';
import {MatSnackBar} from '@angular/material/snack-bar';

@Component({
    selector: 'km-mailing-status',
    templateUrl: './mailing-status.component.html',
    styleUrls: ['./mailing-status.component.sass']
})
export class MailingStatusComponent implements OnInit {

    public mailing: any;
    public mailingStatus: any;
    public mailingError: any;

    constructor(private route: ActivatedRoute,
                private mailingService: MailingService,
                private snackBar: MatSnackBar) {
    }

    async ngOnInit() {
        this.route.params.subscribe(async params => {
            this.mailing = await this.mailingService.getMailing(params.id);

            const resultsSub = this.mailingService.getDataTrackingResults(params.trackingKey)
                .subscribe((results: any) => {
                    this.mailingStatus = results;
                    if (results.status === 'COMPLETED') {
                        resultsSub.unsubscribe();
                    } else if (results.status === 'FAILED') {
                        resultsSub.unsubscribe();
                        const errorMessage = results.result;
                        if (errorMessage) {
                            const message = errorMessage.toLowerCase();
                            if (!message.includes('parameter') && !message.includes('required')) {
                                this.snackBar.open(errorMessage, 'Close', {
                                    verticalPosition: 'top'
                                });
                            }
                        }
                    }
                });

        });
    }

}
